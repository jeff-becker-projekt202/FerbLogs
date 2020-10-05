<?php

namespace Ferb\Logs\Cfg;

final class Instantiable
{
    private $class;
    private $constructor_args;
    private $condition;

    public function __construct($config)
    {
        if (!self::is_instantiable($config)) {
            throw new Exception("{$config} is not an instantiable definition");
        }
        $this->class = new \ReflectionClass($config['class']);

        $this->constructor_args = [];
        if (isset($config['args']) && is_array($config['args'])) {
            $constructor = $this->class->getConstructor();

            foreach ($constructor->getParameters() as $param) {
                $this->constructor_args[] = new InstantiableConstructorArg($config['args'][$param->name], $param);
            }
        }
        $this->condition = Invokeable::is_invokeable($config['condition']) ? new Invokeable($config['condition']) : null;
    }

    public static function is_instantiable($config)
    {
        return is_array($config) && isset($config['class']) && is_string($config['class']);
    }

    public static function instantiate_instance($config)
    {
        if (!self::is_instantiable($config)) {
            return (new self($config))->instantiate();
        }
        return null;
    }

    public function instantiate()
    {
        if (!isset($this->condition) || $this->condition->invoke()) {
            return $this->class->newInstanceArgs($this->get_args());
        }

        return null;
    }

    private function get_args()
    {
        $result = [];
        foreach ($this->constructor_args as $arg) {
            $result[] = $arg->get_value();
        }

        return $result;
    }
}

// try {
//     if ($config['condition']) {
//         $should_do = $this->get_arg_value($config['condition'], true);
//         if (!$should_do) {
//             return null;
//         }
//     }
//     $cls = new \ReflectionClass($config['class']);
//     $constructor = $cls->getConstructor();
//     $args = [];
//     foreach ($constructor->getParameters() as $param) {
//         $args[] = $this->get_param_value($config, $param);
//     }

//     return $cls->newInstanceArgs($args);
// } catch (Exception $ex) {
//     return null;
// }

    // public static function is_interpolated_array($arg)
    // {
    //     return is_array($arg) && isset($arg['callable']);
    // }
    // private function get_arg_value($arg, $default_value)
    // {
    //     if ($this->is_interpolated_array($arg)) {

    //     }

    //     return isset($arg) ? $arg : $default_value;
    // }

// 'file' => [
//     'class' => 'Monolog\\Handler\\RotatingFileHandler',
//     'args' => [
//         'level' => 400,
//         'filename' => [
//             'callable' => 'T21BBAddon\\Logs\\WordpressLogging::get_log_file',
//             'args' => [
//                 'plugin_name' => 't21-bb-addon',
//             ],
//         ],
//         'useLocking' => true,
//     ],
// ],
