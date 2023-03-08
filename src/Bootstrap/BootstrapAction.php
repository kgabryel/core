<?php

namespace Frankie\Core\Bootstrap;

abstract class BootstrapAction
{
    abstract public function execute(): bool;
}
