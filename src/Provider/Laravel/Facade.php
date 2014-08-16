<?php
namespace Eleme\Rlock\Provider\Laravel;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return 'rlock';
    }
}
