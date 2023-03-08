<?php

namespace Frankie\Core\Provider;

use Closure;
use Ds\Map;
use Frankie\Routing\DataProviderInterface;
use Frankie\Routing\Parser\ResourceRoute;

class ResourceRoutingProvider implements DataProviderInterface
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

    public function add(string $key, ResourceRoute $data): self
    {
        $this->data->put($key, $data);

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
