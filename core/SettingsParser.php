<?php declare(strict_types=1);

namespace Homestead\Core;

final class SettingsParser {
    const General = 'general';
    const General_Debug = 'debug';
    const Path = 'path';
    const Path_Controller = 'controller';
    const Path_View = 'view';

    static function createConfig(string $clientDir) {
        $config = self::loadConfig($clientDir);
        self::validateConfigStructure($config);
        return new Config(
            $config[self::General][self::General_Debug],
            $config[self::Path][self::Path_Controller],
            $config[self::Path][self::Path_View],
        );
    }

    private static function loadConfig(string $clientDir): array {
        $settingsFile = $clientDir . '\settings.ini';
        if(!file_exists($settingsFile)) {
            throw new SettingsException("Settings file $settingsFile does not exist.");
        }

        $config = parse_ini_file($settingsFile, true, INI_SCANNER_TYPED);
        if(!$config) {
            throw new SettingsException("Failed to parse settings.ini.");
        }

        return $config;
    }

    private static function validateConfigStructure(array $config): void {
        if(!array_key_exists(self::General, $config)) {
            throw new SettingsException('Settings section "' . self::General . '" not found.');
        }
        $section = $config[self::General];
        if(!array_key_exists(self::General_Debug, $section)) {
            throw new SettingsException('Setting "' . self::General . ':' . self::General_Debug . '" not found.');
        }

        if(!array_key_exists(self::Path, $config)) {
            throw new SettingsException('Settings section "' . self::Path . '" not found.');
        }
        $section = $config[self::Path];
        if(!array_key_exists(self::Path_Controller, $section)) {
            throw new SettingsException('Setting "' . self::Path . ':' . self::Path_Controller . '" not found.');
        }
        if(!array_key_exists(self::Path_View, $section)) {
            throw new SettingsException('Setting "' . self::Path . ':' . self::Path_View . '" not found.');
        }
    }
}

class SettingsException extends KernelException {}