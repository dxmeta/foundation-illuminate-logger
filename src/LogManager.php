<?php

namespace Sunong\Foundation\Log;

use Illuminate\Log\LogManager as LaravelLogManager;
use Sunong\Foundation\Log\Formatter\JsonFormatter;

class LogManager extends LaravelLogManager
{
    /**
     * Get a Monolog formatter instance.
     *
     * @return \Monolog\Formatter\FormatterInterface
     */
    protected function formatter()
    {
        return tap(new JsonFormatter, function ($formatter) {
            $formatter->includeStacktraces();
        });
    }
}
