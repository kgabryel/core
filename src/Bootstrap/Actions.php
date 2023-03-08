<?php

namespace Frankie\Core\Bootstrap;

use Ds\Queue;

class Actions
{
    private Queue $actions;

    public function __construct()
    {
        $this->actions = new Queue();
    }

    public function addAction(BootstrapAction $action): self
    {
        $this->actions->push($action);

        return $this;
    }

    public function getActions(): Queue
    {
        return $this->actions->copy();
    }
}
