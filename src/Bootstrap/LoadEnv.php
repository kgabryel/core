<?php

namespace Frankie\Core\Bootstrap;

use Dotenv\Dotenv;
use Ds\Map;
use Frankie\Core\App;
use Frankie\Core\AppHelper;

class LoadEnv extends BootstrapAction
{
    public function execute(): bool
    {
        $dotenv = Dotenv::createMutable(AppHelper::basePath());
        $env = $dotenv->load();
        App::get()->setConfig(new Map($env));

        return true;
    }
}
