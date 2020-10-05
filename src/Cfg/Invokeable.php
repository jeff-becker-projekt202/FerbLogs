<?php

namespace Ferb\Logs\Cfg;

final class Invokeable
{
    private $callable;
    private $args;

    public function __construct($config)
    {
        if (!self::is_invokeable($config)) {
            throw new Exception("{$config} is not an invokeable definition");
        }
        $this->callable = $config['callable'];
        $this->args = $config['args'] ?? [];
    }

    public static function is_invokeable($config)
    {
        return is_array($config) && isset($config['callable']) && (is_string($config['callable']) || is_callable($config['callable']));
    }

    public function invoke()
    {
        return \call_user_func_array($this->callable, $this->args);
    }
}
