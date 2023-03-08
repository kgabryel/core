<?php

namespace Frankie\Core;

use Frankie\Core\Provider\DataParserProvider;
use Frankie\Core\Provider\ResourceRoutingProvider;
use Frankie\Core\Provider\ResponseFactoryProvider;
use Frankie\Core\Provider\RoutingPathProvider;
use Frankie\DIContainer\DIContainer;
use Frankie\Request\Parser\ParserInterface;
use Frankie\Request\Request\RequestInterface;
use Frankie\Response\Factory\ResponseFactory;
use Frankie\Response\ResponseInterface;
use Frankie\Routing\Route\CorrectRoute;

class Router
{
    private CorrectRoute $correctRoute;
    private ResponseInterface $response;
    private RequestInterface $request;
    private DIContainer $container;
    private ResponseFactoryProvider $responseFactoryProvider;

    public function __construct(ResponseInterface $response, RequestInterface $request, DIContainer $container)
    {
        $this->response = $response;
        $this->request = $request;
        $this->container = $container;
    }

    public function findRoute(
        ResourceRoutingProvider $resourceRoutingProvider,
        RoutingPathProvider $routingPathProvider
    ): bool {
        $correctRoute = RoutingService::findRoute($this->request, $resourceRoutingProvider, $routingPathProvider);
        if ($correctRoute === null) {
            $this->response->withStatus(404)->send();

            return false;
        }
        $this->correctRoute = $correctRoute;

        return true;
    }

    public function findDataType(ResponseFactoryProvider $responseFactoryProvider): bool
    {
        $this->responseFactoryProvider = $responseFactoryProvider;
        $dataType = RoutingService::findDataFormat($this->correctRoute, $this->request, $responseFactoryProvider);
        if ($dataType === null) {
            $this->response->withStatus(406)->send();

            return false;
        }

        return true;
    }

    public function getCorrectRoute(): ?CorrectRoute
    {
        return $this->correctRoute;
    }

    public function createAction(): self
    {
        $actionContainer = new ActionContainer($this->correctRoute, $this->container);
        $result = $actionContainer->execute();
        if ($result instanceof ResponseInterface) {
            $this->response = $result;
        } elseif (isset($this->responseFactoryProvider->get()[$this->correctRoute->getResponseMimeType()])) {
            $factoryName = $this->responseFactoryProvider->get()[$this->correctRoute->getResponseMimeType()];
            /** @var ResponseFactory $factory */
            $factory = new $factoryName();
            $this->response = $factory->setBody($result)->get($this->response);
        }

        return $this;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function executeAfterActions(): void
    {
        $afterActions = new AfterContainer($this->response, $this->correctRoute, $this->container);
        $afterActions->execute();
    }

    public function setContent(DataParserProvider $dataParserProvider): bool
    {
        if (!$this->request->getHeaders()->hasHeader('content-type')) {
            return true;
        }
        $contentType = strtolower($this->request->getHeaders()->getHeader('content-type'));
        $position = strpos($contentType, ';');
        if ($position) {
            $contentType = substr($contentType, 0, $position);
        }
        if (!isset($dataParserProvider->get()[$contentType])) {
            $this->response->withStatus(415)->send();

            return false;
        }
        $name = $dataParserProvider->get()[$contentType];
        /** @var ParserInterface $parser */
        $parser = new $name(file_get_contents('php://input'));
        if (!$parser->isCorrect()) {
            $this->response->withStatus(400)->send();

            return false;
        }
        $parser->parse();
        $this->request->setContent($parser->get());

        return true;
    }
}
