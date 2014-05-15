<?php

use Eleme\Rlock\Predis;

require_once(__DIR__.'/../vendor/autoload.php');

$redis = new Predis();
$lock = $redis->lock('lock1');
echo $lock->acquire() ? 'true' : 'fasel', "\n";
