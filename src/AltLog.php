<?php

namespace SrcLab\AltLog;

use SrcLab\AltLog\Contracts\AltLog as AltLogContract;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Illuminate\Log\ParsesLogConfiguration;
use InvalidArgumentException;

class AltLog implements AltLogContract
{
    use ParsesLogConfiguration;

    /**
     * The log instances.
     *
     * @var array
     */
    protected $log_instances;

    /**
     * Config.
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new Log instance.
     */
    function __construct()
    {
        $this->config = config('alt-log');
    }

    /**
     * Create and get the logger instance.
     *
     * @param string $name
     * @return \SrcLab\AltLog\Logger
     */
    public function file($name)
    {
        if (isset($this->log_instances[$name])) {
            return $this->log_instances[$name];
        } else {
            return $this->log_instances[$name] = $this->createLogInstance($name);
        }
    }


    /**
     * Create the logger instance.
     *
     * @param string $file_name
     * @return \SrcLab\AltLog\Logger
     * @throws \Exception
     */
    protected function createLogInstance($file_name)
    {
        $config = $this->getConfig($file_name);

        $handler = $this->getLogHandler($config);

        $handler->setFormatter($this->formatter());

        $log = new Logger($this->channel());

        return $log->pushHandler($handler);
    }

    /**
     * Get the log handler.
     *
     * @param array $config
     * @return \Monolog\Handler\RotatingFileHandler|\Monolog\Handler\StreamHandler
     * @throws \Exception
     */
    protected function getLogHandler(array $config)
    {
        $driver = $config['driver'] ?? 'daily';

        switch ($driver)
        {
            case 'daily':
                return $this->getDailyHandler($config);

            case 'single':
                return $this->getSingleHandler($config);

            default:
                throw new InvalidArgumentException('Invalid log driver.');
        }
    }

    /**
     * Get the daily handler.
     *
     * @param array $config
     * @return \Monolog\Handler\RotatingFileHandler
     */
    protected function getDailyHandler(array $config)
    {
        return new RotatingFileHandler(
            $config['path'],
            $config['max_files'] ?? 7,
            $this->level($config),
            $config['bubble'] ?? true,
            $config['permission'] ?? null,
            $config['locking'] ?? false
        );
    }

    /**
     * Get the single handler.
     *
     * @param array $config
     * @return \Monolog\Handler\StreamHandler
     * @throws \Exception
     */
    protected function getSingleHandler(array $config)
    {
        return new StreamHandler(
            $config['path'],
            $this->level($config),
            $config['bubble'] ?? true,
            $config['permission'] ?? null,
            $config['locking'] ?? false
        );
    }

    /**
     * Get the name of the log "channel".
     *
     * @return string
     */
    protected function channel()
    {
        return app()->environment();
    }

    /**
     * Get fallback log channel name.
     *
     * @return string
     */
    protected function getFallbackChannelName()
    {
        return app()->bound('env') ? app()->environment() : 'production';
    }

    /**
     * Get config.
     *
     * @param string $file_name
     * @return mixed
     */
    protected function getConfig($file_name)
    {
        $config = $this->config['logging']['custom_log'][$file_name] ?? $this->config['logging']['default'];

        if (empty($config['path'])) {
            $config['path'] = \Str::finish($this->config['alt_logs_path'], '/').$file_name.'.log';
        }

        return $config;
    }

    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function formatter()
    {
        return tap(new LineFormatter(null, 'Y-m-d H:i:s', true, true), function ($formatter) {
            /* @var \Monolog\Formatter\LineFormatter $formatter */
            $formatter->includeStacktraces();
        });
    }
}
