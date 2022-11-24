<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

final class SettingsParser {
    const GENERAL = 'general';
    const GENERAL_DEBUG = 'debug';
    const GENERAL_ENABLE_AUTHENTICATION = 'enable_authentication';
    const GENERAL_LOG_LEVEL = 'log_level';
    const PATH = 'path';
    const PATH_CONTROLLER = 'controller';
    const PATH_VIEW = 'view';
    const PATH_STATIC = 'static';

    public static function createConfig(string $clientDir) {
        $config = self::loadConfig($clientDir);
        self::validateConfigStructure($config);
        return new Config(
            $config[self::GENERAL][self::GENERAL_DEBUG],
            $config[self::GENERAL][self::GENERAL_ENABLE_AUTHENTICATION],
            $config[self::GENERAL][self::GENERAL_LOG_LEVEL],
            $config[self::PATH][self::PATH_CONTROLLER],
            $config[self::PATH][self::PATH_VIEW],
            $config[self::PATH][self::PATH_STATIC]
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
        if(!array_key_exists(self::GENERAL, $config)) {
            throw new SettingsException('Settings section "' . self::GENERAL . '" not found.');
        }
        $section = $config[self::GENERAL];
        if(!array_key_exists(self::GENERAL_DEBUG, $section)) {
            throw new SettingsException('Setting "' . self::GENERAL . ':' . self::GENERAL_DEBUG . '" not found.');
        }
        if(!array_key_exists(self::GENERAL_ENABLE_AUTHENTICATION, $section)) {
            throw new SettingsException('Setting "' . self::GENERAL . ':' . self::GENERAL_ENABLE_AUTHENTICATION . '" not found.');
        }
        if(!array_key_exists(self::GENERAL_LOG_LEVEL, $section)) {
            throw new SettingsException('Setting "' . self::GENERAL . ':' . self::GENERAL_LOG_LEVEL . '" not found.');
        }

        if(!array_key_exists(self::PATH, $config)) {
            throw new SettingsException('Settings section "' . self::PATH . '" not found.');
        }
        $section = $config[self::PATH];
        if(!array_key_exists(self::PATH_CONTROLLER, $section)) {
            throw new SettingsException('Setting "' . self::PATH . ':' . self::PATH_CONTROLLER . '" not found.');
        }
        if(!array_key_exists(self::PATH_VIEW, $section)) {
            throw new SettingsException('Setting "' . self::PATH . ':' . self::PATH_VIEW . '" not found.');
        }
        if(!array_key_exists(self::PATH_STATIC, $section)) {
            throw new SettingsException('Setting "' . self::PATH . ':' . self::PATH_STATIC . '" not found.');
        }
    }
}

class SettingsException extends Exception {}