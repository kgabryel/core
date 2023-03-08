<?php

namespace Frankie\Core\Bootstrap;

use Frankie\Core\App;
use Frankie\Core\AppHelper;
use Frankie\Core\Db\ConfigGetter;
use Frankie\Core\Db\EntityManagerFactory;

class ConnectToDatabase extends BootstrapAction
{
    public function execute(): bool
    {
        $factory = new EntityManagerFactory(new ConfigGetter($_ENV), AppHelper::basePath('Entities'));
        $container = App::get()->getDIContainer();
        $container->setExistsObject($factory->build()->get());

        return true;
    }
}
