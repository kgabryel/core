<?php

namespace Frankie\Core;

use Closure;
use Ds\Vector;
use Frankie\DIContainer\DIContainer;
use Frankie\Request\Request\RequestInterface;
use Frankie\Routing\Action\Action;
use Frankie\Routing\Action\ControllerAction;
use Frankie\Routing\Route\CorrectRoute;

class ActionContainer
{
    private CorrectRoute $route;
    private mixed $stack;
    private DIContainer $container;

    public function __construct(CorrectRoute $route, DIContainer $container)
    {
        $this->route = $route;
        $this->container = $container;
        $this->stack = $this->before()(...);
    }

    private function before(): Closure
    {
        if ($this->route->getBefore()->isEmpty()) {
            return (new ControllerAction(
                $this->route->getControllerName(),
                $this->route->getActionName(),
                $this->container
            ))(...);
        }
        $next = $this->route->getBefore()->shift();

        if (!$this->container->hasKey($next)) {
            $this->container->setNewObject($next);
        }
        $request = $this->container->get(RequestInterface::class);

        return (new Action($this->container->get($next), 'handle', new Vector([$this->before(), $request])))(...);
    }

    public function execute(): mixed
    {
        $result = $this->stack;
        while (is_callable($result)) {
            $result = $result();
        }

        return $result;
    }
}
