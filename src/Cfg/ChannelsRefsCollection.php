<?php

namespace Ferb\Logs\Cfg;

use Monolog\Handler\NullHandler;

class ChannelsRefsCollection
{
    private $channels;
    private $is_only_default;

    public function __construct($cfg)
    {
        $this->channels = [];
        if (isset($cfg['*'])) {
            foreach ($cfg as $key => $config) {
                if (isset($config['handlers'])) {
                    $this->channels[$key] = new ChannelRef($key, $config);
                }
            }
        }
    }

    public function create($all_handlers, $all_processors, $requested_channel)
    {
        if (empty($this->channels)) {
            return [[new NullHandler()], []];
        }
        list($channel, $prefix) = $this->find_best_channel($requested_channel);
        $handlers = [];
        $processors = [];

        if (isset($channel)) {
            foreach ($channel->get_handlers() as $ref => $handler) {
                $handlers[] = $handler->find($all_handlers);
            }
            foreach ($channel->get_processors() as $ref) {
                $processors[] = $all_processors[$ref];
            }
        }

        $handlers = array_filter($handlers);
        $processors = array_filter($processors);
        if (empty($handlers)) {
            $handlers[] = new NullHandler();
        }

        return [$handlers, $processors];
    }

    private function find_best_channel($requested_channel)
    {
        $possible_channels = array_filter($this->channels, function ($key) use ($requested_channel) {
            return 0 == strpos($key, $requested_channel);
        }, ARRAY_FILTER_USE_KEY);
        if (empty($possible_channels)) {
            if (!isset($this->channels['*'])) {
                return [null, null];
            }
            $possible_channels = [$this->channels['*']];
        }
        $keys = array_keys($possible_channels);
        uasort($keys, function ($a, $b) {
            strlen($a) - strlen($b);
        });

        return [$possible_channels[$keys[0]], $keys[0]];
    }
}
