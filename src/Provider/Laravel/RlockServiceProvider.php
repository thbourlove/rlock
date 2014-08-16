<?php
namespace Eleme\Rlock\Provider\Laravel;

use Illuminate\Support\ServiceProvider;
use Eleme\Rlock\Factory;

class RlockServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bindShared('rlock', function($app) {
            return new Factory($app['redis']);
        });
    }
}
