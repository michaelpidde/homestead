<?php declare(strict_types=1);

namespace Homestead\Core\Attribute;

use \Attribute;

#[Attribute]
final class Parameter {
    function __construct(
        private string $name,
        private string $pattern
    ) {}

    function name(): string {
        return $this->name;
    }

    function pattern(): string {
        return $this->pattern;
    }
}