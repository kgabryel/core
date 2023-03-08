<?php

namespace Frankie\Core\Provider;

use Frankie\Core\AppHelper;
use Frankie\Core\Logger\PathProviderInterface;

class LogPathProvider implements PathProviderInterface
{
    public function getPath(): string
    {
        return AppHelper::basePath(
            sprintf('log%slogs%s%s.log', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, date('d-m-Y'))
        );
    }
}
