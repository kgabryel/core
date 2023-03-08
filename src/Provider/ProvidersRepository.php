<?php

namespace Frankie\Core\Provider;

use Ds\Map;

class ProvidersRepository
{
    private Map $providers;

    public function __construct()
    {
        $this->providers = new Map();
    }

    public function register(string $provider, string $path): self
    {
        $this->providers->put($provider, $path);

        return $this;
    }

    public function get(string $provider): object
    {
        if (!$this->providers->hasKey($provider)) {
            return new $provider;
        }

        return require $this->providers->get($provider);
    }
}
