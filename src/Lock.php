<?php
namespace Eleme\Rlock;

class Lock
{
    const DEFAULT_TIMEOUT = 5000;
    const DEFAULT_INTERVAL = 500;
    const DEFAULT_BLOCKING = true;
    const DEFAULT_SUFFIX = ":lock";

    private $lockname = '';
    private $redis = null;
    private $validity = 0;
    private $options = array();

    public function __construct($redis, $lockname, $options = array())
    {
        $this->redis = $redis;
        $this->options = array_merge(array(
            'timeout' => self::DEFAULT_TIMEOUT,
            'interval' => self::DEFAULT_INTERVAL,
            'blocking' => self::DEFAULT_BLOCKING,
            'suffix' => self::DEFAULT_SUFFIX,
        ), $options);
        $this->lockname = $lockname.$this->options['suffix'];
    }

    public function lock()
    {
        while (true) {
            $validity = $this->options['timeout'] / 1000 + microtime(true);
            if ($this->redis->setnx($this->lockname, $validity)) {
                $this->validity = $validity;
                return true;
            }

            $existing = $this->redis->get($this->lockname);
            if ($existing < microtime(true)) {
                $existing = $this->redis->getset($this->lockname, $validity);
                if ($existing < microtime(true)) {
                    $this->validity = $validity;
                    return true;
                }
            }

            if (!$this->options['blocking']) {
                return false;
            }
            usleep($this->options['interval'] * 1000);
        }
    }

    public function release()
    {
        if ($this->validity === 0) {
            throw new \RuntimeException("Unlocked lock cannot be released!");
        }
        $existing = $this->redis->get($this->lockname);
        if ($existing >= microtime(true)) {
            $this->redis->del($this->lockname);
        }
        $this->validity = 0;
        return true;
    }

    public function __destruct()
    {
        if ($this->validity !== 0) {
            $this->release();
        }
    }
}
