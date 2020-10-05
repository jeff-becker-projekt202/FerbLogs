<?php

declare(strict_types=1);

namespace Ferb\Logs\Tests;

use Ferb\Logs\LoggerFactory;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class LoggerFactoryTests extends TestCase
{
    public function testSanaitizeCfgEnsuresHandlers()
    {
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('handlers', $c);
    }

    public function testSanaitizeCfgEnsuresProcessors()
    {
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('processors', $c);
    }

    public function testSanaitizeCfgEnsuresChannels()
    {
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('channels', $c);
        $this->assertArrayHasKey('*', $c['channels']);
        $this->assertArrayHasKey('level', $c['channels']['*']);
    }

    public function testSanaitizeCfgEnsuresNotNull()
    {
        $c = LoggerFactory::sanitize_config(null);
        $this->assertNotNull($c);
    }

    public function testCreateNullHandler()
    {
        $f = new LoggerFactory([]);
        $h = $f->create_logger(self::class);
        $this->assertNotNull($h);
    }

    public function testCanCreateDemoLogger()
    {
        $f = new LoggerFactory($this->getLoggerConfig());

        $logger = $f->create_logger('hello\\world');
        $this->assertNotNull($logger);
    }

    public static function getLogFile($file)
    {
        return "c:\\foo\\bar\\{$file}.log";
    }

    private function getLoggerConfig()
    {
        return [
            'channels' => [
                '*' => [
                    'level' => Logger::ERROR,
                    'handlers' => [
                        'newrelic' => 100,
                        'file' => 100,
                        'error_log' => 100,
                    ],
                ],
            ],
            'handlers' => [
                'newrelic' => [
                    'class' => 'Monolog\\Handler\\NewRelicHandler',
                    'condition' => [
                        'callable' => 'extension_loaded',
                        'args' => ['newrelic'],
                    ],
                ],
                //ErrorLogHandler
                'error_log' => [
                    'class' => 'Monolog\\Handler\\ErrorLogHandler',
                ],
                'file' => [
                    'class' => 'Monolog\\Handler\\RotatingFileHandler',
                    'args' => [
                        'level' => 400,
                        'filename' => [
                            'callable' => 'Ferb\\Logs\\Tests\\LoggerFactoryTests::getLogFile',
                            'args' => ['hello-world'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
