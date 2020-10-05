<?php

namespace Ferb\Logs\Cfg;

final class FilteringHandler extends \Monolog\Handler\HandlerWrapper
{
    private $filter;
    private $level;
    private $channel;

    public function __construct($level, $channel, $filter, $inner)
    {
        parent::__construct($inner);
        $this->filter = $filter;
        $this->level = $level;
        $this->channel = $channel;
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
}
