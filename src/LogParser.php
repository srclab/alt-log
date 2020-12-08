<?php

namespace SrcLab\AltLog;

use SrcLab\AltLog\Exceptions\LogNotFoundException;
use SrcLab\AltLog\Exceptions\LargeLogException;
use SplFileObject;

class LogParser
{
    /**
     * Create a new Log instance.
     */
    function __construct()
    {
        $this->config = config('alt-log');
    }

    /**
     * Parse log.
     *
     * @param string $path
     * @return array
     * @throws \SrcLab\AltLog\Exceptions\LargeLogException
     * @throws \SrcLab\AltLog\Exceptions\LogNotFoundException
     */
    public function parseLog($path)
    {
        if (!file_exists($path)) {
            throw new LogNotFoundException;
        }

        if (filesize($path) > $this->config['max_file_size']) {
            throw new LargeLogException;
        }

        $file = new SplFileObject($path, 'r');

        $data = [];

        $i = 0;
        while (!$file->eof())
        {
            $line_data = $this->parseLogLine($file->current(), $i+1);

            if (!empty($line_data)) {
                $data[$i] = $line_data;
                $i++;
            } else {

                if ($i!=0) {
                    $context = $this->clearLine($file->current());
                    if (!empty($context)) {
                        $data[$i-1]['context'][] = $context;
                    }
                }
            }

            $file->next();

        }

        return $data;
    }

    /**
     * Parse log line.
     *
     * @param string $record
     * @param int $record_number
     * @return array
     */
    protected function parseLogLine($record, $record_number)
    {
        $pattern = '/\[(?P<date>\d{4}-\d{2}-\d{2}[T\s]?\d{2}:\d{2}:\d{2}(\.\d{6}\+\d{2}\:\d{2})?)\]\s(?P<channel>\w+).(?P<level>\w+):\s(?P<message>.*[^ ]+)/';

        preg_match($pattern, $record, $data);

        if(!empty($data)) {
            return [
                'number' => $record_number,
                'date' => $data['date'],
                'channel' => $data['channel'],
                'level' => $data['level'],
                'message' => $this->clearLine($data['message']),
                'context' => [],
            ];
        } else {
            return [];
        }
    }

    /**
     * Clear line.
     *
     * @param string $str
     * @return string
     */
    protected function clearLine($str)
    {
        $str = str_replace("\n", '', $str);

        return $str;
    }
}
