<?php

namespace App\Logger\Handler;

use Monolog\Handler\RotatingFileHandler as BaseRotatingFileHandler;

class RotatingFileHandler extends BaseRotatingFileHandler
{
    public function __construct()
    {
        parent::__construct(
            $this->getLogFile(),
            $this->maxFiles,
            $this->level,
            $this->bubble,
            $this->filePermission,
            $this->useLocking
        );
    }

    private function getLogFile(): string
    {
        return dirname(__DIR__, 2) . '/logs/symfony.log';
    }
}
