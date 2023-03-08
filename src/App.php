<?php

namespace Frankie\Core;

use Ds\Map;
use Frankie\Core\Bootstrap\Actions;
use Frankie\Core\Provider\ConfigProvider;
use Frankie\Core\Provider\DataParserProvider;
use Frankie\Core\Provider\InterfaceMappingProvider;
use Frankie\Core\Provider\RequestProvider;
use Frankie\Core\Provider\ResourceRoutingProvider;
use Frankie\Core\Provider\ResponseFactoryProvider;
use Frankie\Core\Provider\RoutingPathProvider;
use Frankie\Core\Provider\RulesProvider;
use Frankie\DIContainer\DIContainer;
use Frankie\ExceptionHandler\HandlersProvider;
use Frankie\Request\Request\RequestInterface;
use RuntimeException;

final class App
{
    private static App $instance;
    private string $basePath;
    private Map $config;
    private DIContainer $container;
    private ResourceRoutingProvider $resourceRoutingProvider;
    private RulesProvider $rulesProvider;

    public function __construct(
        string $basePath,
        DIContainer $container,
        ResourceRoutingProvider $resourceRoutingProvider,
        RulesProvider $rulesProvider
    ) {
        $this->basePath = sprintf('%s%s', rtrim($basePath, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
        $this->container = $container;
        $this->config = new Map();
        $this->resourceRoutingProvider = $resourceRoutingProvider;
        $this->rulesProvider = $rulesProvider;
    }

    public static function run(
        string $basePath,
        RoutingPathProvider $routingPathProvider,
        ResponseFactoryProvider $responseFactoryProvider,
        RequestProvider $requestProvider,
        ConfigProvider $configProvider,
        InterfaceMappingProvider $interfaceMappingProvider,
        ResourceRoutingProvider $resourceRoutingProvider,
        DataParserProvider $dataParserProvider,
        RulesProvider $rulesProvider,
        Actions $bootstrapActions,
        ?HandlersProvider $handlersProvider = null
    ): void {
        $container = new DIContainer();
        self::$instance = new self($basePath, $container, $resourceRoutingProvider, $rulesProvider);
        $responseFactoryProvider->executeFunction();
        $resourceRoutingProvider->executeFunction();
        $dataParserProvider->executeFunction();
        $rulesProvider->executeFunction();
        if ($responseFactoryProvider->get()->isEmpty()) {
            throw new RuntimeException(sprintf(Exceptions::NO_PARSERS, ResponseFactoryProvider::class));
        }

        $configRepository = $configProvider->get();
        $container->setExistsObject($configRepository);
        $container->setInterfacesMapping($interfaceMappingProvider->get()->get())
            ->setConfigRepository($configRepository);
        if ($handlersProvider !== null) {
            $container->setExistsObject($handlersProvider);
        }
        self::bootstrap($bootstrapActions);
        $request = $requestProvider->get();
        $container->setExistsObject($request);
        self::routerExecute(
            $request,
            $container,
            $resourceRoutingProvider,
            $responseFactoryProvider,
            $dataParserProvider,
            $routingPathProvider
        );
    }

    public static function get(): self
    {
        return self::$instance;
    }

    private static function bootstrap(Actions $actions): void
    {
        $actionsList = $actions->getActions();
        while (!$actionsList->isEmpty()) {
            $action = $actionsList->pop();
            if (!$action->execute()) {
                throw new RuntimeException(sprintf(Exceptions::BOOTSTRAP_ERROR, $action::class));
            }
        }
    }

    private static function routerExecute(
        RequestInterface $request,
        DIContainer $container,
        ResourceRoutingProvider $resourceRoutingProvider,
        ResponseFactoryProvider $responseFactoryProvider,
        DataParserProvider $dataParserProvider,
        RoutingPathProvider $routingPathProvider
    ): void {
        $router = new Router(AppHelper::getResponse(), $request, $container);
        if (!$router->findRoute($resourceRoutingProvider, $routingPathProvider)) {
            return;
        }
        $container->setExistsObject($router->getCorrectRoute());
        if (!$router->findDataType($responseFactoryProvider)) {
            return;
        }
        if (!$router->setContent($dataParserProvider)) {
            return;
        }
        $response = $router->createAction()->getResponse();
        $response->send();
        $router->executeAfterActions();
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getConfig(): Map
    {
        return $this->config->copy();
    }

    public function setConfig(Map $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getDIContainer(): DIContainer
    {
        return $this->container;
    }

    public function getResourceRoutingProvider(): ResourceRoutingProvider
    {
        return $this->resourceRoutingProvider;
    }

    public function getRulesProvider(): RulesProvider
    {
        return $this->rulesProvider;
    }
}
