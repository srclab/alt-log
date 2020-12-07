<?php

namespace SrcLab\AltLog\Support\Facades;

class AltLog extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'srclab.alt_log';
    }
}
