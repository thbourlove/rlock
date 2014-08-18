<?php

function sigint() {
    exit;
}

pcntl_signal(SIGINT, 'sigint');
pcntl_signal(SIGTERM, 'sigint');

use Eleme\Rlock\Lock;
use Predis\Client;

require_once(__DIR__.'/../vendor/autoload.php');

$redis = new Client();

$lock1 = new Lock($redis, 'lock1');
$lock1->acquire();

posix_kill(posix_getpid(), SIGINT);

pcntl_signal_dispatch();
