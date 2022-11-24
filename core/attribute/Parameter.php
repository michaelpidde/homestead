<?php declare(strict_types=1);

namespace Homestead\Core\Attribute;

use \Attribute;

#[Attribute]
final class Parameter {
    public function __construct(
        private string $name,
        private string $pattern
    ) {}

    public function name(): string {
        return $this->name;
    }

    public function pattern(): string {
        return $this->pattern;
    }
}