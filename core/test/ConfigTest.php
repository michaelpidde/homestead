<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Homestead\Core\Config;

final class ConfigTest extends TestCase {
    function testCreation() {
        $config = new Config();
        $this->assertFalse($config->debugEnabled());
        $this->assertFalse($config->authenticationEnabled());
        $this->assertEquals('ERROR', $config->logLevel());
        $this->assertEquals('', $config->controllerDir());
        $this->assertEquals('', $config->viewDir());
        $this->assertEquals('', $config->staticDir());
    }
}