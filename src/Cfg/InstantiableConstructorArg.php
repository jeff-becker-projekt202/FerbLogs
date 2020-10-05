<?php

namespace Ferb\Logs\Cfg;

class InstantiableConstructorArg
{
    private $name;

    private $literal_value;
    private $closure;
    private $closure_args;
    private $is_closure;

    public function __construct($config, $param)
    {
        $this->name = $param->name;

        if (self::is_interpolated_array($config)) {
            $this->is_closure = true;
            $this->closure = $config['callable'];
            $this->closure_args = $config['args'];
        } else {
            $this->is_closure = false;
            $default_value = $param->isDefaultValueAvailable ? $param->getDefaultValue() : null;
            $this->literal_value = isset($config) ? $config : $default_value;
        }
    }

    public function get_value()
    {
        if (!$this->is_closure) {
            return $this->literal_value;
        }

        return  \call_user_func_array($this->closure, $this->closure_args);
    }

    public static function is_interpolated_array($arg)
    {
        return is_array($arg) && isset($arg['callable']);
    }
}
