<?php

abstract class HpsBuilderAbstract
{
    /** @var array(HpsBuilderAction) */
    public $builderActions = array();

    /** @var bool */
    public $executed       = false;

    /** @var array(callable) */
    public $validations    = array();

    /** @var HpsRestGatewayService */
    protected $service     = null;

    /**
     * @param HpsGatewayServiceAbstract $service
     *
     * @return HpsBuilderAbstract
     */
    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * @return HpsBuilderAbstract
     */
    public function execute()
    {
        foreach ($this->builderActions as $action) {
            call_user_func_array($action->action, $action->arguments);
        }
        $this->validate();
        $this->executed = true;
        return $this;
    }

    /**
     * @return HpsBuilderAbstract
     */
    public function addAction($action)
    {
        $this->builderActions[] = $action;
        return $this;
    }

    /**
     * @throws HpsException
     */
    public function checkStatus()
    {
        if (!$this->executed) {
            throw new HpsException('Builder actions not executed');
        }
    }

    /**
     * Allows for automatic setter functions
     * in child classes.
     *
     * @param string $name
     * @param array  $args
     *
     * @throws HpsUnknownPropertyException
     *
     * @return HpsBuilderAbstract
     */
    public function __call($name, array $args)
    {
        switch (true) {
            case substr($name, 0, 4) == 'with':
                $property = substr($name, 4);
                $property = strtolower(substr($property, 0, 1)) . substr($property, 1);
                $this->setPropertyIfExists($property, $args[0]);
                break;
            default:
                return false;
                break;
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    protected function validate()
    {
        $actions = $this->compileActionCounts();
        foreach ($this->validations as $validation) {
            $result = call_user_func_array($validation['callback'], array($actions));
            if (!$result) {
                $class = $validation['exceptionType'];
                throw new $class($validation['exceptionMessage'], 0);
            }
        }
    }

    /**
     * @return array
     */
    protected function compileActionCounts()
    {
        $counts = array();

        foreach ($this->builderActions as $action) {
            $counts[$action->name] = isset($counts[$action->name]) ? $counts[$action->name]+1 : 1;
        }

        return $counts;
    }

    /**
     * @param callable $callback
     * @param string   $exceptionType
     * @param string   $exceptionMessage
     *
     * @return HpsBuilderAbstract
     */
    protected function addValidation($callback, $exceptionType, $exceptionMessage = '')
    {
        $this->validations[] = array(
            'callback' => $callback,
            'exceptionType' => $exceptionType,
            'exceptionMessage' => $exceptionMessage,
        );
        return $this;
    }

    /**
     * Sets a property if it exists on the current object.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @throws HpsUnknownPropertyException
     *
     * @return null
     */
    private function setPropertyIfExists($property, $value)
    {
        if (property_exists($this, $property)) {
            if ($value == null) {
                return $this;
            }

            $action = new HpsBuilderAction($property, array($this, 'setProperty'));
            $action->arguments = array($property, $value);
            $this->addAction($action);
        } else {
            throw new HpsUnknownPropertyException($this, $property);
        }
    }

    /**
     * Sets a property on the current object.
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return null
     */
    protected function setProperty($property, $value)
    {
        $this->{$property} = $value;
    }
}
