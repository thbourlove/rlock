<?php

use Eleme\Rlock\Factory;
use Predis\Client;

require_once(__DIR__.'/../vendor/autoload.php');

$factory = new Factory(new Client);
$lock = $factory->make('test');
