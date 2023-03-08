<?php

namespace Frankie\Core\Provider;

use Closure;

class RoutingPathProvider
{
    private Closure $resourceRoutesFunction;
    private array $resourceRouteParams;

    private Closure $additionalRoutesFunction;
    private array $additionalRouteParams;

    public function setResourceRoutesPath(Closure $createFunction, array $params = []): void
    {
        $this->resourceRoutesFunction = $createFunction;
        $this->resourceRouteParams = $params;
    }

    public function getResourceRoutesPath(): string
    {
        $function = $this->resourceRoutesFunction;

        return $this->resourceRouteParams === [] ? $function() : $function(...$this->resourceRouteParams);
    }

    public function setAdditionalRoutesPath(Closure $createFunction, array $params = []): void
    {
        $this->additionalRoutesFunction = $createFunction;
        $this->additionalRouteParams = $params;
    }

    public function getAdditionalRoutesPath(): string
    {
        $function = $this->additionalRoutesFunction;

        return $this->additionalRouteParams === [] ? $function() : $function(...$this->additionalRouteParams);
    }
}
