<?php

namespace Ferb\Logs\Cfg;

class HandlerRef
{
    private $ref_name;
    private $level;
    private $prefix;
    private $filter;
    private $formatter;

    public function __construct($ref_name, $level, $prefix, $filter, $formatter)
    {
        $this->ref_name = $ref_name;
        $this->level = $level;
        $this->prefix = $prefix;
        $this->filter = isset($filter) ? \Closure::fromCallable($filter) : function ($record) {
            return true;
        };
        if (isset($formatter)) {
            if (!($formatter instanceof Instantiable)) {
                $formatter = new Instantiable($formatter);
            }
            $this->formatter = $formatter;
        } else {
            $this->formatter = null;
        }
    }

    public function find($all_handlers)
    {
        if (!isset($all_handlers[$this->ref_name])) {
            return null;
        }
        $result = new class($this->level, $this->prefix, $this->filter, $all_handlers[$this->ref_name], $this->formatter) extends \Monolog\Handler\HandlerWrapper {
            private $filter;
            private $level;
            private $channel;

            public function __construct($level, $channel, $filter, $inner, $formatter)
            {
                parent::__construct($inner);
                $this->filter = $filter;
                $this->level = $level;
                $this->channel = $channel;
                if (!empty($formatter)) {
                    $this->setFormatter($formatter);
                }
            }

            public function isHandling(array $record)
            {
                if (1 == count(array_keys($record)) && isset($record['level'])) {
                    return $record['level'] >= $this->level;
                }
                $is_this_channel = '*' == $this->channel || (0 == strpos($record['channel'], $this->channel));
                $is_higher_level = $record['level'] >= $this->level;
                $f = $this->filter;
                $is_allowed = $f($record);

                return $is_allowed && $is_this_channel && $is_higher_level;
            }

            public function handle(array $record)
            {
                if ($this->isHandling($record)) {
                    return $this->handler->handle($record);
                }

                return true;
            }
        };

        return $result;
    }
}
