<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel;

use Homestead\Core\PageViewModel;

class HomeViewModel implements PageViewModel {
    public function __construct(private string $title = 'Clozer Woods', private array $nav = []) {}

    public function title(): string {
        return $this->title;
    }

    public function nav(): array {
        return $this->nav;
    }
}