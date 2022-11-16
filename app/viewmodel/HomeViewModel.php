<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel;

use Homestead\Core\PageViewModel;

class HomeViewModel implements PageViewModel {
    function __construct(private string $title = 'Clozer Woods', private array $nav = []) {}

    function title(): string {
        return $this->title;
    }

    function nav(): array {
        return $this->nav;
    }
}