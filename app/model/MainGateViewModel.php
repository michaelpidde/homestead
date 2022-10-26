<?php declare(strict_types=1);

namespace Clozerwoods\Model;

use Homestead\Core\PageViewModel;

class MainGateViewModel implements PageViewModel {
    function title(): string {
        return 'Main Gate';
    }

    function nav(): array {
        return [];
    }
}