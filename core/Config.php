<?php declare(strict_types=1);

namespace Homestead\Core;

final class Config {
    function __construct(
        private bool $debug = false,
        private string $controllerDir = '',
        private string $viewDir = '',
    ) {}

    function debugEnabled(): bool {
        return $this->debug;
    }

    function controllerDir(): string {
        return $this->controllerDir;
    }

    function viewDir(): string {
        return $this->viewDir;
    }
}