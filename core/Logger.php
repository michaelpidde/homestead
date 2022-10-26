<?php declare(strict_types=1);

namespace Homestead\Core;

final class Logger {
    const LOG_DIR = '.log';
    const MAX_LOGS = 5;
    const INFO = 'INFO';
    const ERROR = 'ERROR';

    static $logLevel = 'ERROR';

    static function setLevel(string $level) {
        if(in_array($level, [self::INFO, self::ERROR])) {
            self::$logLevel = $level;
        }
    }

    static function error(string $message, mixed $data = null): void {
        self::log(self::ERROR, $message, $data);
    }

    static function info(string $message, mixed $data = null): void {
        self::log(self::INFO, $message, $data);
    }

    static function cull(): void {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . self::LOG_DIR;
        if(!is_dir($dir)) {
            return;
        }
        $files = glob($dir . DIRECTORY_SEPARATOR . '*.log');
        if(count($files) <= self::MAX_LOGS) {
            return;
        }
        // Sort with newest files first
        usort($files, function($a, $b) {
            return filectime($b) - filectime($a);
        });
        for($i = self::MAX_LOGS; $i < count($files); ++$i) {
            unlink($files[$i]);
        }
    }

    private static function levelToInt(string $level): int {
        switch($level) {
            case self::INFO:
                return 0;
            case self::ERROR:
                return 1;
            default:
                return -1;
        }
    }

    private static function shouldLog(string $level): bool {
        return self::levelToInt($level) >= self::levelToInt(self::$logLevel);
    }

    private static function log(string $level, string $message, mixed $data = null): void {
        if(!self::shouldLog($level)) {
            return;
        }

        $handle = self::getDailyFile();
        if($handle) {
            $line = date_format(date_create(), "Y-m-d H:i O ") . "$level: $message\n" . (($data !== null) ? json_encode($data) : '');
            fwrite($handle, $line);
            fclose($handle);
        }
    }

    private static function getDailyFile(): mixed {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . self::LOG_DIR;
        $file = $dir . DIRECTORY_SEPARATOR . date_format(date_create(), "Y-m-d") . '.log';
        if(!is_dir($dir)) {
            mkdir($dir);
        }
        return fopen($file, 'a');
    }
}
