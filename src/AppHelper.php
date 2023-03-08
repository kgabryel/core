<?php

namespace Frankie\Core;

use Frankie\Request\Request\RequestInterface;
use Frankie\Response\ResponseInterface;

class AppHelper
{
    public static function basePath(string $path = ''): string
    {
        return sprintf('%s%s', App::get()->getBasePath(), ltrim($path, DIRECTORY_SEPARATOR));
    }

    public static function redirect(string $path, int $status = 302): ResponseInterface
    {
        return self::getResponse()->withStatus($status)->withHeader('Location', $path);
    }

    public static function getResponse(): ResponseInterface
    {
        $container = App::get()->getDIContainer();
        if (!$container->hasKey(ResponseInterface::class)) {
            $container->setNewObject($container->getInterfaceMapping(ResponseInterface::class));
        }

        return $container->get(ResponseInterface::class);
    }

    public static function baseURL(string $path = ''): string
    {
        if (App::get()->getConfig()->hasKey('BASE_URL')) {
            return sprintf('%s/%s', ltrim(App::get()->getConfig()->get('BASE_URL'), '/'), rtrim($path, '/'));
        }
        $container = App::get()->getDIContainer();
        $request = $container->get(RequestInterface::class);

        return sprintf('%s/%s', rtrim($request->getBaseURL(), '/'), ltrim($path, '/'));
    }
}
