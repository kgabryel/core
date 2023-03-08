<?php

namespace Frankie\Core\Provider;

use Closure;
use Ds\Map;

abstract class Provider
{
    protected Map $data;
    protected ?Closure $setFunction;
    protected array $params;

    public function __construct()
    {
        $this->data = new Map();
        $this->setFunction = null;
        $this->params = [];
    }

    public function add(string $key, string $value): self
    {
        $this->data->put($key, $value);

        return $this;
    }

    public function executeFunction(): self
    {
        if ($this->setFunction !== null) {
            $function = $this->setFunction;
            $this->params === [] ? $function() : $function(...$this->params);
        }

        return $this;
    }

    public function get(): Map
    {
        return $this->data->copy();
    }
}
