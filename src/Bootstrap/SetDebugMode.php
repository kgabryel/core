<?php

namespace Frankie\Core\Bootstrap;

use Frankie\Core\App;
use Frankie\ExceptionHandler\HandlerQueue;
use Whoops\Run;

class SetDebugMode extends BootstrapAction
{
    public function execute(): bool
    {
        $env = App::get()->getConfig()->get('APP_ENV');
        if ($env === 'PROD') {
            ini_set('display_errors', 0);
            ini_set('html_errors', 0);
            ini_set('display_startup_errors', 0);
        }
        $debug = (int)App::get()->getConfig()->get('DEBUG');
        $container = App::get()->getDIContainer();
        $container->setNewObject(HandlerQueue::class);
        /** @var HandlerQueue $factory */
        $factory = $container->get(HandlerQueue::class);
        $whoops = new Run();
        $factory->create($debug);
        while (!$factory->isEmpty()) {
            $whoops->appendHandler($factory->pop());
        }
        $whoops->register();

        return true;
    }
}
