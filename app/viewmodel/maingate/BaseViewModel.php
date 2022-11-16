<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel\MainGate;

use Homestead\Core\PageViewModel;

class BaseViewModel implements PageViewModel {
    function __construct(private string $title = 'Main Gate', private array $nav = []) {}

    function title(): string {
        return $this->title;
    }

    function nav(): array {
        return $this->nav;
    }
}