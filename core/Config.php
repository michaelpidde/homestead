<?php declare(strict_types=1);

namespace Homestead\Core;

final class Config {
    function __construct(
        private bool $debug = false,
        private bool $authenticationEnabled = false,
        private string $controllerDir = '',
        private string $viewDir = '',
        private string $staticDir = '',
    ) {}

    function debugEnabled(): bool {
        return $this->debug;
    }

    function authenticationEnabled(): bool {
        return $this->authenticationEnabled;
    }

    function controllerDir(): string {
        return $this->controllerDir;
    }

    function viewDir(): string {
        return $this->viewDir;
    }

    function staticDir(): string {
        return $this->staticDir;
    }
}