<?php

/**
 * Class HpsBuilderAction
 */
class HpsBuilderAction
{
    /** @var callable|null */
    public $action    = null;

    /** @var string|null */
    public $name      = null;

    /** @var array */
    public $arguments = null;
    /**
     * HpsBuilderAction constructor.
     *
     * @param null $name
     * @param null $action
     */
    public function __construct($name = null, $action = null)
    {
        $this->name = $name;
        $this->action = $action;
    }
}
