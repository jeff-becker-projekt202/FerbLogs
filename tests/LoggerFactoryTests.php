<?php

declare(strict_types=1);

namespace Ferb\Logs\Tests;

use Ferb\Logs\LoggerFactory;
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
}
