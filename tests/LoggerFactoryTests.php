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

    public function test_sanaitize_cfg_ensures_handlers(){
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('handlers', $c);
    }
    public function test_sanaitize_cfg_ensures_processors(){
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('processors', $c);
    }
    public function test_sanaitize_cfg_ensures_channels(){
        $c = LoggerFactory::sanitize_config([]);
        $this->assertArrayHasKey('channels', $c);
        $this->assertArrayHasKey('*', $c['channels']);
        $this->assertArrayHasKey('level', $c['channels']['*']);
    }
    public function test_sanaitize_cfg_ensures_not_null(){
        $c = LoggerFactory::sanitize_config(null);
        $this->assertNotNull($c);
    }
       
    public function test_create_null_handler(){
        $f = new LoggerFactory([]);
        $h = $f->create_logger(self::class);
        $this->assertNotNull($h);
    }
    
}
