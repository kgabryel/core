<?php

namespace Frankie\Core\Provider;

use Frankie\Core\AppHelper;
use Frankie\ExceptionHandler\PathProviderInterface;

class LogExceptionPathProvider implements PathProviderInterface
{
    public function getPath(): string
    {
        return AppHelper::basePath(
            sprintf('log%sexceptions%s%s.log', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, date('d-m-Y'))
        );
    }
}
