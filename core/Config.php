<?php declare(strict_types=1);

namespace Homestead\Core;

final class Config {
    public function __construct(
        private bool $debug = false,
        private bool $authenticationEnabled = false,
        private string $logLevel = 'ERROR',
        private string $controllerDir = '',
        private string $viewDir = '',
        private string $staticDir = '',
    ) {}

    public function debugEnabled(): bool {
        return $this->debug;
    }

    public function authenticationEnabled(): bool {
        return $this->authenticationEnabled;
    }

    public function logLevel(): string {
        return $this->logLevel;
    }

    public function controllerDir(): string {
        return $this->controllerDir;
    }

    public function viewDir(): string {
        return $this->viewDir;
    }

    public function staticDir(): string {
        return $this->staticDir;
    }
}