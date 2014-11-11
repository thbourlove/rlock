<?php
namespace Eleme\Rlock\Tests;

use Eleme\Rlock\Lock;
use Mockery;

class LockTest extends \PHPUnit_Framework_TestCase
{
    public function testAcquireSuccess()
    {
        $redis = Mockery::mock('Predis\Client');
        $redis->shouldReceive('set')->andReturn(true);

        $lockname = 'test';
        $lock = new Lock($redis, $lockname);
        $result = $lock->acquire();
        $this->assertTrue($result);

        $redis->shouldReceive('transaction');
    }

    public function testAcquireFailed()
    {
        $redis = Mockery::mock('Predis\Client');
        $redis->shouldReceive('set')->andReturn(false);

        $lockname = 'test';
        $lock = new Lock($redis, $lockname, array('blocking' => false));
        $result = $lock->acquire();
        $this->assertFalse($result);

        $redis->shouldReceive('transaction');
    }

    public function testLocked()
    {
        $timeout = 1000;
        $interval = 100;
        $time = microtime(true);

        $redis = Mockery::mock('Predis\Client');

        $lockname = 'test';
        $lock = new Lock($redis, $lockname, array('timeout' => $timeout, 'interval' => $interval));
        $lock2 = new Lock($redis, $lockname);
        $beforeLocked = microtime(true);

        $redis->shouldReceive('exists')->times(1)->andReturn(false);
        $this->assertFalse($lock2->locked());

        $redis->shouldReceive('exists')->andReturn(true);
        $this->assertTrue($lock2->locked());

        $redis->shouldReceive('transaction');
    }

    public function testRelease()
    {
        $redis = Mockery::mock('Predis\Client');
        $redis->shouldReceive('set')->andReturn(true);

        $lockname = 'test';
        $lock = new Lock($redis, $lockname);
        $result = $lock->acquire();
        $this->assertTrue($result);

        $redis->shouldReceive('transaction');

        $result = $lock->release();
        $this->assertTrue($result);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testReleaseFailed()
    {
        $redis = Mockery::mock('Predis\Client');

        $lockname = 'test';
        $lock = new Lock($redis, $lockname);
        $lock->release();
        $redis->shouldReceive('transaction');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDestruct()
    {
        $redis = Mockery::mock('Predis\Client');
        $redis->shouldReceive('set')->andReturn(true);

        $lockname = 'test';
        $lock = new Lock($redis, $lockname);
        $result = $lock->acquire();
        $this->assertTrue($result);

        $redis->shouldReceive('transaction');

        $lock->__destruct();
        $lock->release();
    }
}
