<?php
namespace Eleme\Rlock;

class Factory
{
    private $redis = null;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function make($lockname, $options = array())
    {
        return new Lock($this->redis, $lockname, $options);
    }
}
