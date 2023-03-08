<?php

namespace Frankie\Core;

use Ds\Sequence;
use Frankie\DIContainer\DIContainer;
use Frankie\Response\ResponseInterface;
use Frankie\Routing\Action\AfterAction;
use Frankie\Routing\Route\CorrectRoute;
use InvalidArgumentException;
use ReflectionClass;

class AfterContainer
{
    private ResponseInterface $response;

    private Sequence $actions;

    private DIContainer $container;

    public function __construct(ResponseInterface $response, CorrectRoute $route, DIContainer $container)
    {
        $this->actions = $route->getAfter();
        $this->response = $response;
        $this->container = $container;
    }

    public function execute(): self
    {
        while (!$this->actions->isEmpty()) {
            $name = $this->actions->pop();
            $classReflection = new ReflectionClass($name);
            if (!$classReflection->isSubclassOf(AfterAction::class)) {
                throw new InvalidArgumentException(
                    sprintf(Exceptions::INVALID_AFTER_ACTION, $name, AfterAction::class)
                );
            }
            if (!$this->container->hasKey($name)) {
                $this->container->setNewObject($name);
            }
            $action = $this->container->get($name);
            $status = $action->handle($this->response);
            if (!$status) {
                break;
            }
        }

        return $this;
    }
}
