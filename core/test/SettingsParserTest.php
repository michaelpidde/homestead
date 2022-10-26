<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Homestead\Core\Config;
use Homestead\Core\SettingsParser;
use Homestead\Core\SettingsException;

final class SettingsParserTest extends TestCase {
    private string $settingsFile = '';

    function setUp(): void {
        $this->settingsFile = sys_get_temp_dir() . '\settings.ini';
    }

    function tearDown(): void {
        if(file_exists($this->settingsFile)) {
            unlink($this->settingsFile);
        }
    }

    function testCreateConfig_AllSettings() {
        $this->writeSettingsFile($this->arrayToIni($this->getSettingsArray()));
        $config = SettingsParser::createConfig(sys_get_temp_dir());
        $this->assertTrue($config instanceof Config);
    }

    function testCreateConfig_WithoutGeneralSection() {
        $config = $this->getSettingsArray();
        unset($config['general']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Settings section "general" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutDebugSetting() {
        $config = $this->getSettingsArray();
        unset($config['general']['debug']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Setting "general:debug" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutAuthenticationSetting() {
        $config = $this->getSettingsArray();
        unset($config['general']['enable_authentication']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Setting "general:enable_authentication" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutPathSection() {
        $config = $this->getSettingsArray();
        unset($config['path']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Settings section "path" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutControllerSetting() {
        $config = $this->getSettingsArray();
        unset($config['path']['controller']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Setting "path:controller" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutViewSetting() {
        $config = $this->getSettingsArray();
        unset($config['path']['view']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Setting "path:view" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    function testCreateConfig_WithoutStaticSetting() {
        $config = $this->getSettingsArray();
        unset($config['path']['static']);
        $this->writeSettingsFile($this->arrayToIni($config));
        $this->expectException(SettingsException::class);
        $this->expectExceptionMessage('Setting "path:static" not found.');
        $config = SettingsParser::createConfig(sys_get_temp_dir());
    }

    private function getSettingsArray(): array {
        return [
            'general' => [
                'debug' => 'true',
                'enable_authentication' => 'true',
            ],
            'path' => [
                'controller' => 'controller',
                'view' => 'view',
                'static' => 'static',
            ],
        ];
    }

    private function writeSettingsFile(string $content): void {
        file_put_contents($this->settingsFile, $content);
    }

    private function arrayToIni(array $settings): string {
        $ini = '';
        foreach($settings as $key => $value) {
            if(gettype($key) != 'string') {
                throw new Exception("Invalid key $key");
            }

            if(gettype($value) == 'array') {
                $ini .= "[$key]\n" . $this->arrayToIni($value);
            } else {
                $ini .= "$key = $value\n";
            }
        }

        return $ini;
    }
}