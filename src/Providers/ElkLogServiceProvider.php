<?php

namespace Sunong\Foundation\Log\Providers;

use Sunong\Foundation\Log\LogManager;
use Illuminate\Log\LogServiceProvider;

class ElkLogServiceProvider extends LogServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('log', function ($app) {
            $logManager = new LogManager($app);

            // 特别注意:
            // 1. 非命令行模式, 以下代码自动初始化一个 trace_id
            // 2. 如果是命令行模式, 也可自定义一个 processor 传入 trace_id
            //    但是比较推荐直接使用超全局变量 $GLOBALS['sn_trace_id']
            //    在长驻进程的情况下便于实时更换日志标记

            if (PHP_SAPI !== 'cli') {
                if (defined('REQUEST_UUID')) {
                    $traceId = REQUEST_UUID;
                } elseif (isset($_SERVER['REQUEST_UUID'])) {
                    $traceId = $_SERVER['REQUEST_UUID'];
                } else {
                    $traceId = uniqid('trace_id_') . mt_rand(1000, 9999);
                }

                $logManager->pushProcessor(function ($record) use ($traceId) {
                    $record['trace_id'] = $traceId;
                    return $record;
                });
            }

            return $logManager;
        });
    }
}
