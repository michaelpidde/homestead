<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel\MainGate;

use Clozerwoods\Model\Page;

class PageViewModel extends BaseViewModel {
    private ?Page $selectedPage = null;
    private array $pages = [];
    private bool $modified = false;

    function _selectedPage(Page $value) {
        $this->selectedPage = $value;
    }

    function selectedPage(): Page {
        return $this->page;
    }

    function _pages(array $value) {
        $this->pages = $value;
    }

    function pages(): array {
        return $this->pages;
    }

    function _modified(bool $value) {
        $this->modified = $value;
    }

    function modified(): bool {
        return $this->modified;
    } 
}