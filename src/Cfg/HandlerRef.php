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
        $result = new FilteringHandler($this->level, $this->prefix, $this->filter, $all_handlers[$this->ref_name]);
        if (isset($this->formatter)) {
            $result->setFormatter($this->formatter);
        }

        return $result;
    }
}
