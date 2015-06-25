<?php

class HpsBuilderAction
{
    /** @var callable|null */
    public $action    = null;

    /** @var string|null */
    public $name      = null;

    /** @var array */
    public $arguments = null;

    public function __construct($name = null, $action = null)
    {
        $this->name = $name;
        $this->action = $action;
    }
}
