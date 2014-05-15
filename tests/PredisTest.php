<?php
namespace Eleme\Rlock\Tests;

use Mockery;
use Eleme\Rlock\Predis;
use Eleme\Rlock\Lock;

class PredisTest extends \PHPUnit_Framework_TestCase
{
    public function testLock()
    {
        $redis = new Predis();
        $lock = $redis->lock('test');
        $this->assertEquals(get_class($lock), 'Eleme\Rlock\Lock');
    }
}
