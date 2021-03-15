<?php

namespace SrcLab\AltLog;

use Illuminate\Filesystem\Filesystem;
use SrcLab\AltLog\Exceptions\InvalidLogNameException;
use InvalidArgumentException;

class LogManager
{
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Config.
     *
     * @var array
     */
    protected $config;

    /**
     * LogManager constructor.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->config = config('alt-log');
    }

    /**
     * Get log path.
     *
     * @param string $log
     * @return string
     * @throws \SrcLab\AltLog\Exceptions\InvalidLogNameException
     */
    public function getLogPath($log)
    {
        $pars_log_name = explode('/', $log);

        if (count($pars_log_name) != 3) {
            throw new InvalidLogNameException('Invalid log name format.');
        }

        return $this->getLogDirectory($pars_log_name[1]).'/'.$pars_log_name[2];
    }

    /**
     * Get log info.
     *
     * @param string $log
     * @return array
     * @throws \SrcLab\AltLog\Exceptions\InvalidLogNameException
     */
    public function getLogInfo($log)
    {
        $pars_log_name = explode('/', $log);

        if (count($pars_log_name) != 3) {
            throw new InvalidLogNameException('Invalid log name format.');
        }

        return [
            'log' => $log,
            'name' => explode('.', $pars_log_name[2])[0],
            'type' => $pars_log_name[1],
            'project' => $pars_log_name[0],
        ];
    }

    /**
     * Get logs list.
     *
     * @return array
     */
    public function getLogsList()
    {
        $result_list = [];

        /**
         * Laravel логи.
         */
        $result_list = array_merge($result_list, $this->formatTypeLogsList($this->getLogsListByPath($this->getLogDirectory('laravel')), 'laravel'));

        /**
         * Alt логи.
         */
        $result_list = array_merge($result_list, $this->formatTypeLogsList($this->getLogsListByPath($this->getLogDirectory('alt')), 'alt'));


        return $result_list;
    }

    /**
     * Get grouping list
     *
     * @return array
     */
    public function getGroupingList()
    {
        $result_list = [];

        if (!empty($this->config['grouping'])) {
            foreach ($this->config['grouping'] as $value) {
                $result_list[$value['system_name']] = [
                    'pattern' => $value['pattern'],
                    'name' => $value['name'],
                ];
            }
        }

        $result_list['default'] = [
            'name' => __('alt-log::general.server.default_log'),
        ];


        return $result_list;
    }

    /**
     * Delete log.
     *
     * @param string $log
     * @throws \SrcLab\AltLog\Exceptions\InvalidLogNameException
     */
    public function deleteLog($log)
    {
        $path = $this->getLogPath($log);

        if ($this->filesystem->exists($path)) {
            $this->filesystem->delete($path);
        }
    }

    /**
     * Format type logs list.
     *
     * @param array $logs_list
     * @param string $type
     * @return array
     */
    protected function formatTypeLogsList(array $logs_list, $type)
    {
        if (empty($logs_list)) {
            return [];
        }

        $result_list = [];

        foreach ($logs_list as $log) {
            $result_list[] = [
                'log' => "main/$type/$log",
                'name' => explode('.', $log)[0],
                'type' => $type,
                'project' => 'main',
                'group' => $this->getGroupForLog($log),
            ];
        }

        return $result_list;
    }

    /**
     * Get group for log.
     *
     * @param string $log_name
     * @return string
     */
    protected function getGroupForLog($log_name)
    {
        foreach ($this->getGroupingList() as $key => $value) {
            if (!empty($value['pattern']) && \Str::is($value['pattern'], $log_name)) {
                return $key;
            }
        }

        return 'default';
    }

    /**
     * Get logs list by path.
     *
     * @param string $path
     * @return array
     */
    protected function getLogsListByPath($path)
    {
        if (!$this->filesystem->exists($path)) {
            return [];
        }

        $logs_list = [];

        $all = scandir($path);

        if (!empty($all)) {

            foreach ($all as $file) {

                if($file == '..' || $file == '.') continue;

                if (!is_dir($path.'/'.$file)) {

                    $file_info = pathinfo($file);

                    if (!empty($file_info['extension']) && $file_info['extension'] == 'log') {
                        $logs_list[] = $file;
                    }
                }
            }
        }

        return $logs_list;
    }

    /**
     * Get log directory.
     *
     * @param string $type
     * @return string
     */
    protected function getLogDirectory($type)
    {
        switch ($type)
        {
            case 'laravel':
                return storage_path('logs');

            case 'alt':
                return rtrim($this->config['alt_logs_path'], '/');

            default:
                throw new InvalidArgumentException('Invalid log type.');
        }
    }
}
