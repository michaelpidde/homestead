<?php declare(strict_types=1);

namespace Clozerwoods\Model;

class NavItem {
    function __construct(private string $route, private string $label) {}

    function route(): string {
        return $this->route;
    }

    function label(): string {
        return $this->label;
    }
}