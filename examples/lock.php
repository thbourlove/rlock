<?php

use Eleme\Rlock\Lock;
use Predis\Client;

require_once(__DIR__.'/../vendor/autoload.php');

$redis = new Client();

$lock1 = new Lock($redis, 'lock1');
$lock1->acquire();
// release it by manually or it will be autoreleased.
$lock1->release();


// pass some options like timeout and interval.
$lock2 = new Lock($redis, 'lock2', array('timeout' => 5000, 'interval' => 500));
echo $lock2->acquire() ? 'true' : 'false', "\n";

// sometimes you may need a non-block lock.
$lock3 = new Lock($redis, 'lock3', array('blocking' => false));
echo $lock3->acquire() ? 'true' : 'false', "\n";
$lock4 = new Lock($redis, 'lock3', array('blocking' => false));
echo $lock4->acquire() ? 'true' : 'false', "\n";
