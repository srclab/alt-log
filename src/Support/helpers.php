<?php

if (!function_exists('alt_log')) {
    /**
     * AltLog.
     *
     * @return \SrcLab\AltLog\Contracts\AltLog
     */
    function alt_log()
    {
        return app(\SrcLab\AltLog\Contracts\AltLog::class);
    }
}
