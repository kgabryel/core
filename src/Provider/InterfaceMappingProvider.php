<?php

namespace Frankie\Core\Provider;

use Closure;

class InterfaceMappingProvider
{
    private Closure $createFunction;
    private array $params;

    public function set(Closure $createFunction, array $params = []): void
    {
        $this->createFunction = $createFunction;
        $this->params = $params;
    }

    public function get(): InterfaceMapping
    {
        $function = $this->createFunction;

        return $this->params === [] ? $function() : $function(...$this->params);
    }
}
