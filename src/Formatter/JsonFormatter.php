<?php

namespace Sunong\Foundation\Log\Formatter;

use Monolog\Formatter\JsonFormatter as MonologJsonFormatter;

class JsonFormatter extends MonologJsonFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $record)
    {
        /**
         * 根据运维方的配置进行格式化的日志信息
         *
         * @var  array
         */
        $formated = [
            'time' => $record['datetime']->format('Y-m-d H:i:s'),
            'level' => $record['level_name'],
            'log' => [
                'message' => $record['message'],
                'context' => '',
                'extra' => empty($record['extra']) ? '' : var_export($record['extra'], true),
            ],
            'trace_id' => $record['trace_id'] ?? $GLOBALS['sn_trace_id'] ?? '',
        ];

        if (! empty($record['context'])) {
            // 记录请求信息
            if (isset($record['context']['logstash-request'])) {
                $formated['request'] = $record['context']['logstash-request'];

                unset($record['context']['logstash-request']);
            }

            // 记录生命周期
            if (isset($record['context']['logstash-response'])) {
                $formated['response'] = [
                    'status' => $record['context']['logstash-response']['status'] ?? 200,
                    'elapsed' => $record['context']['logstash-response']['elapsed'] ?? 0,
                ];

                unset($record['context']['logstash-response']);
            }

            // 处理context为字符
            empty($record['context']) || $formated['log']['context'] = var_export($record['context'], true);
        }

        return $this->toJson($this->normalize($formated), true) . ($this->appendNewline ? "\n" : '');
    }
}
