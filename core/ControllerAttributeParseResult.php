<?php declare(strict_types=1);

namespace Homestead\Core;

final class ControllerAttributeParseResult {
    public function __construct(
        private array $routes,
        private array $warnings
    ) {}

    public function routes(): array { 
        return $this->routes;
    }
    
    public function warnings(): array {
        return $this->warnings;
    }
}