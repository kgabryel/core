<?php

namespace Frankie\Core\Db;

use Ds\Map;

final class ConfigGetter
{
    private Map $dbConfig;

    private bool $devMode;

    public function __construct(array $configs)
    {
        $this->dbConfig = new Map();
        $this->create($configs);
    }

    private function create(array $configs): void
    {
        if (isset($configs['DATABASE_URL'])) {
            $this->dbConfig->put('url', $configs['DATABASE_URL']);
        }
        foreach ($configs as $key => $val) {
            if (strncmp($key, 'DB_', 3) === 0) {
                $this->push($key, $val);
            }
        }
        if (isset($configs['APP_ENV']) && $configs['APP_ENV'] === 'prod') {
            $this->devMode = false;
        } else {
            $this->devMode = true;
        }
    }

    private function push(string $key, mixed $val): void
    {
        $key = strtolower(substr($key, 3));
        $this->dbConfig->put($key, $val);
    }

    public function get(): Map
    {
        return $this->dbConfig->copy();
    }

    public function isDevMode(): bool
    {
        return $this->devMode;
    }
}
