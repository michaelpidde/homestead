<?php declare(strict_types=1);

namespace Clozerwoods\Model;

use Homestead\Core\PageViewModel;

class HomeViewModel implements PageViewModel {
    function title(): string {
        return 'Home';
    }
}