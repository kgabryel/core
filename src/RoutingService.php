<?php

namespace Frankie\Core;

use Frankie\Core\Provider\ResourceRoutingProvider;
use Frankie\Core\Provider\ResponseFactoryProvider;
use Frankie\Core\Provider\RoutingPathProvider;
use Frankie\Request\Request\RequestInterface;
use Frankie\Routing\Manager\ResourceRouteManager;
use Frankie\Routing\Manager\RouteManager;
use Frankie\Routing\Parser\ResourceRouteParser;
use Frankie\Routing\Parser\RouteParser;
use Frankie\Routing\Route\CorrectRoute;
use Frankie\Routing\Route\CorrectRouteFactory;
use Frankie\Routing\RoutePriority;
use Frankie\Routing\Router;
use Frankie\Routing\Validator\ResourceRouteValidator;
use Frankie\Routing\Validator\RouteValidator;
use Symfony\Component\Yaml\Yaml;

class RoutingService
{
    public const ROUTING_PRIORITY = 'ROUTING_PRIORITY';

    public static function findRoute(
        RequestInterface $request,
        ResourceRoutingProvider $resourceRoutingProvider,
        RoutingPathProvider $routingPathProvider
    ): ?CorrectRoute {
        $routingMode = RoutePriority::from((int)($_ENV[self::ROUTING_PRIORITY] ?? 0));
        $additionalRouteManager = new RouteManager(
            $request,
            new RouteValidator(),
            new RouteParser(),
            Yaml::parseFile($routingPathProvider->getAdditionalRoutesPath())
        );
        $resourceRouteManager = new ResourceRouteManager(
            $request,
            new ResourceRouteValidator(),
            new ResourceRouteParser($resourceRoutingProvider),
            Yaml::parseFile($routingPathProvider->getResourceRoutesPath())
        );
        $router = new Router($additionalRouteManager, $resourceRouteManager);
        $correctRouteFactory = new CorrectRouteFactory(
            $router->getAdditionalRoutes(), $router->getResourceRoutes(), $request, $routingMode
        );

        return $correctRouteFactory->find()->get();
    }

    public static function findDataFormat(
        CorrectRoute $correctRoute,
        RequestInterface $request,
        ResponseFactoryProvider $responseFactoryProvider
    ): ?string {
        $preferred = $request->getHeaders()->getPreferredMimeType();
        if ($preferred === null || ($preferred->getValue() === '*/*')) {
            $preferred = $correctRoute->getDataFormat() ??
                App::get()->getConfig()->get(
                    'BASE_DATA_TYPE',
                    array_key_first($responseFactoryProvider->get()->toArray())
                );
        } else {
            $preferred = $preferred->getValue();
        }
        foreach (array_keys($responseFactoryProvider->get()->toArray()) as $format) {
            if ($format === $preferred) {
                $correctRoute->setDataType($format);

                return $format;
            }
        }
        foreach (array_keys($responseFactoryProvider->get()->toArray()) as $format) {
            if (CorrectRouteFactory::compareMimeType($format, $preferred)) {
                $correctRoute->setDataType($format);

                return $format;
            }
        }

        return null;
    }
}
