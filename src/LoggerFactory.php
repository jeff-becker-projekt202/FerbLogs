<?php

namespace Ferb\Logs;

use Monolog\Logger;
use Ferb\Logs\Cfg\ChannelsRefsCollection;
use Ferb\Logs\Cfg\Instantiable;

final class LoggerFactory
{
    private $channels;

    private $all_loggers = [];
    private $all_handlers = [];
    private $all_processors = [];

    public function __construct($cfg)
    {
        $cfg = self::sanitize_config($cfg);
        foreach ($cfg['handlers'] as $key => $config) {
            $this->all_handlers[$key] = Instantiable::instantiate_instance($config);
        }
        foreach ($cfg['processors'] as $key => $config) {
            $this->all_processors[$key] = Instantiable::instantiate_instance($config);
        }
        $this->all_handlers = array_filter($this->all_handlers);
        $this->all_processors = array_filter($this->all_processors);
        $this->channels = new ChannelsRefsCollection($cfg['channels']);
    }

    public function create_logger(string $channel)
    {
        if (!isset($this->all_loggers[$channel])) {
            list($handlers, $processors) = $this->channels->create($this->all_handlers, $this->all_processors, $channel);

            $this->all_loggers[$channel] = new Logger($channel, $handlers, $processors);
        }

        return $this->all_loggers[$channel];
    }

    public static function sanitize_config($cfg)
    {
        $cfg = isset($cfg) ? $cfg : [];

        return array_merge_recursive([
            'handlers' => [],
            'channels' => ['*' => ['level' => Logger::EMERGENCY]],
            'processors' => [],
        ], $cfg ?? []);
    }
}
