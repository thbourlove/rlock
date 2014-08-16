# Rlock
[![Build Status](https://travis-ci.org/thbourlove/rlock.png?branch=master)](https://travis-ci.org/thbourlove/rlock)
[![Coverage Status](https://coveralls.io/repos/thbourlove/rlock/badge.png?branch=master)](https://coveralls.io/r/thbourlove/rlock?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thbourlove/rlock/badges/quality-score.png?s=f113f1ab965f6aaef55e497a330caf72bff94201)](https://scrutinizer-ci.com/g/thbourlove/rlock/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/c5aa62f0-0edc-42e7-af61-2800ab327065/mini.png)](https://insight.sensiolabs.com/projects/c5aa62f0-0edc-42e7-af61-2800ab327065)
[![Stable Status](https://poser.pugx.org/eleme/rlock/v/stable.png)](https://packagist.org/packages/eleme/rlock)

Redis lock for some atomic opration.

## Install With Composer:

```json
"require": {
    "eleme/rlock": "~0.2"
}
```

## Usage

#### Rlock
``` php
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
```

#### Factory
```php
<?php

use Eleme\Rlock\Factory;
use Predis\Client;

require_once(__DIR__.'/../vendor/autoload.php');

$factory = new Factory(new Client);
$lock = $factory->make('test');
```

#### Extended Predis
```php
<?php

use Eleme\Rlock\Predis;

require_once(__DIR__.'/../vendor/autoload.php');

$redis = new Predis();
$lock = $redis->lock('lock1');
echo $lock->acquire() ? 'true' : 'fasel', "\n";
```

#### Laravel

##### Service Provider

`'Eleme\Rlock\Provider\Laravel\RlockServiceProvider'`

##### Facade

`'Rlock'             => 'Eleme\Rlock\Provider\Laravel\Facade'`
