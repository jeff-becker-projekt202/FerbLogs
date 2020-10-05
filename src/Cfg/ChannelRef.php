<?php

namespace Ferb\Logs\Cfg;

class ChannelRef
{
    private $handlers;
    private $prefix;
    private $processors;

    public function __construct($prefix, $config)
    {
        $level = $config['level'] ?? Logger::DEBUG;
        $filter = $config['filter'] ?? null;
        $formatter = isset($conf['formatter']) ? new Instantiable($conf['formatter']) : null;
        $this->handlers = [];
        foreach ($config['handlers'] as $ref => $spec) {
            if (is_int($spec)) {
                $this->handlers[$ref] = new HandlerRef($ref, $level, $prefix, $filter, $formatter);
            } else {
                $this->handlers[$ref] = new HandlerRef(
                    $ref,
                    $spec['level'] ?? $level,
                    $prefix,
                    $spec['filter'] ?? $filter,
                    $spec['formatter'] ?? $formatter
                );
            }
        }
        $this->processors = $config['processors'] ?? [];
    }

    public function get_handlers()
    {
        return $this->handlers;
    }

    public function get_processors()
    {
        return $this->processors;
    }
}
