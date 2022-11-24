<?php declare(strict_types=1);

namespace Clozerwoods\Model;

class NavItem {
    public function __construct(private string $route, private string $label) {}

    public function route(): string {
        return $this->route;
    }

    public function label(): string {
        return $this->label;
    }
}