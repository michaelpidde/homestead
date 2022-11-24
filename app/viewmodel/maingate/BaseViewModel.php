<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel\MainGate;

use Homestead\Core\PageViewModel;

class BaseViewModel implements PageViewModel {
    public function __construct(private string $title = 'Main Gate', private array $nav = []) {}

    public function title(): string {
        return $this->title;
    }

    public function nav(): array {
        return $this->nav;
    }
}