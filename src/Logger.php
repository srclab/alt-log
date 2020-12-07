<?php

namespace SrcLab\AltLog;

use Monolog\Logger as MonologLogger;
use Throwable;

class Logger extends MonologLogger
{
    /**
     * Report exception.
     *
     * @param \Throwable $exception
     * @param string|null $message
     */
    public function exception(Throwable $exception, $message = null)
    {
        if (!empty($message)) {
            $message = $message."\n";
        }

        $this->error($message.$exception->getMessage()."\n".$exception->getTraceAsString());
    }
}
