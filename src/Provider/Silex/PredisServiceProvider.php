<?php
namespace Eleme\Rlock\Provider\Silex;

use Silex\ServiceProviderInterface;
use Silex\Application;
use Eleme\Rlock\Predis;

class PredisServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['redis'] = $app->share(function ($app) {
            return new Predis($app['redis.config']);
        });
        $app['redis.config'] = array();
    }

    public function boot(Application $app)
    {
    }
}
