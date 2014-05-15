<?php
namespace Eleme\Rlock;

use Predis\Client;

class Predis extends Client
{
    public function lock($lockname, $options = array())
    {
        return new Lock($this, $lockname, $options);
    }
}
