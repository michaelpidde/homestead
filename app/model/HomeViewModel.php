<?php declare(strict_types=1);

namespace Clozerwoods\Model;

use Homestead\Core\PageViewModel;

class HomeViewModel implements PageViewModel {
    function title(): string {
        return 'Home';
    }

    function nav(): array {
        return ['Home', 'Page 1', 'Page 2'];
    }
}