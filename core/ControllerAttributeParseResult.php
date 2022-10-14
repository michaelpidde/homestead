<?php declare(strict_types=1);

namespace Homestead\Core;

final class ControllerAttributeParseResult {
    function __construct(
        private array $routes,
        private array $warnings
    ) {}

    function routes(): array { 
        return $this->routes;
    }
    
    function warnings(): array {
        return $this->warnings;
    }
}