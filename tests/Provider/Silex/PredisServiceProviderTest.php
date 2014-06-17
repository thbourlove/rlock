<?php

namespace Eleme\Rlock\Tests\Provider\Silex;

use Silex\Application;
use Eleme\Rlock\Provider\Silex\PredisServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class PredisServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application();
        $app->register(new PredisServiceProvider);
        $app['redis.config'] = array(
            'host' => 'test_host',
            'port' => 6380
        );

        $app->get('/', function () {
        });
        $request = Request::create('/');
        $app->handle($request);

        $this->assertInstanceOf('Eleme\Rlock\Predis', $app['redis']);
    }
}
