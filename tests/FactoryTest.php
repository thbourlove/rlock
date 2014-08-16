<?php
namespace Eleme\Rlock\Tests;

use Mockery;
use Eleme\Rlock\Factory;
use Predis\Client;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testLock()
    {
        $redis = new Client();
        $factory = new Factory($redis);
        $lock = $factory->make('test');
        $this->assertEquals(get_class($lock), 'Eleme\Rlock\Lock');
    }
}
