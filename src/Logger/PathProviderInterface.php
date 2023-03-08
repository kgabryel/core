<?php

namespace Frankie\Core\Logger;

interface PathProviderInterface
{
    public function getPath(): string;
}
