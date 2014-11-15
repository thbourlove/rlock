<?php
namespace Eleme\Rlock;

use Predis\Client;
use Predis\Transaction\AbortedMultiExecException;

class Lock
{
    const DEFAULT_TIMEOUT = 5000;
    const DEFAULT_INTERVAL = 500;
    const DEFAULT_BLOCKING = true;
    const DEFAULT_TRANSACTION = true;
    const DEFAULT_SUFFIX = ":lock";

    private $lockname = '';
    private $redis = null;
    private $validity = false;
    private $options = array();

    public function __construct(Client $redis, $lockname, $options = array())
    {
        $this->redis = $redis;
        $this->options = array_merge(array(
            'timeout' => self::DEFAULT_TIMEOUT,
            'interval' => self::DEFAULT_INTERVAL,
            'blocking' => self::DEFAULT_BLOCKING,
            'suffix' => self::DEFAULT_SUFFIX,
            'transaction' => self::DEFAULT_TRANSACTION,
        ), $options);
        $this->lockname = $lockname.$this->options['suffix'];
    }

    public function acquire()
    {
        while (true) {
            $this->token = uniqid('', true);
            if ($this->redis->set($this->lockname, $this->token, 'NX', 'PX', $this->options['timeout'])) {
                $this->validity = true;
                break;
            }
            if (!$this->options['blocking']) {
                return false;
            }
            usleep($this->options['interval'] * 1000);
        }
        return true;
    }

    public function release()
    {
        if (!$this->validity) {
            throw new \RuntimeException("Unlocked lock cannot be released!");
        }
        return $this->options['transaction'] ? $this->releaseWithTransaction() : $this->releaseWithoutTransaction();
    }

    public function locked()
    {
        return $this->redis->exists($this->lockname);
    }

    public function __destruct()
    {
        if ($this->validity) {
            $this->release();
        }
    }

    private function releaseWithoutTransaction()
    {
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
         ';
        return $this->redis->eval($script, 1, $this->lockname, $this->token);
    }

    private function releaseWithTransaction()
    {
        try {
            $options = array('cas' => true, 'watch' => $this->lockname);
            $this->redis->transaction($options, function ($transaction) {
                if ($transaction->get($this->lockname) === $this->token) {
                    $transaction->multi();
                    $transaction->del($this->lockname);
                }
            });
            $this->validity = false;
        } catch (AbortedMultiExecException $e) {
            throw new \RuntimeException("Lock has been released by another client!");
        }
        return true;
    }
}
