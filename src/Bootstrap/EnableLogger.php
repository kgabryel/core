<?php

namespace Frankie\Core\Bootstrap;

use Frankie\Core\App;
use Frankie\Core\Logger\FileLogHandler;
use Frankie\Core\Logger\Logger;
use Frankie\Core\Logger\LogLevel;
use Frankie\Core\Provider\LogPathProvider;

class EnableLogger extends BootstrapAction
{
    public function execute(): bool
    {
        $level = (int)($_ENV['LOGGING'] ?? 0);
        $logger = new Logger(LogLevel::from($level));

        $container = App::get()->getDIContainer();
        $handler = new FileLogHandler(new LogPathProvider());
        $handler->changeOutputFormat($_ENV['LOG_OUTPUT_FORMAT'] ?? null)
            ->changeDateFormat($_ENV['LOG_DATE_FORMAT'] ?? null);
        $logger->addToAll($handler);
        $container->setExistsObject($logger);

        return true;
    }
}
