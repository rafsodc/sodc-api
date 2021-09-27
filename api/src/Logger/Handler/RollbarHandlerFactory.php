<?php

namespace App\Logger\Handler;

use Psr\Log\LogLevel;
use Rollbar\Monolog\Handler\RollbarHandler;
use Rollbar\Rollbar;

class RollbarHandlerFactory
{
    public function __construct(array $config)
    {
        Rollbar::init($config, false, false, false);
    }

    public function createRollbarHandler(): RollbarHandler
    {
        return new RollbarHandler(Rollbar::logger(), LogLevel::INFO);
    }
}