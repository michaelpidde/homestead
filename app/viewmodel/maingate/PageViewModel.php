<?php declare(strict_types=1);

namespace Clozerwoods\ViewModel\MainGate;

use Clozerwoods\Model\Page;

class PageViewModel extends BaseViewModel {
    private ?Page $selectedPage = null;
    private array $pages = [];
    private bool $modified = false;

    public function _selectedPage(Page $value) {
        $this->selectedPage = $value;
    }

    public function selectedPage(): Page {
        return $this->selectedPage;
    }

    public function _pages(array $value) {
        $this->pages = $value;
    }

    public function pages(): array {
        return $this->pages;
    }

    public function _modified(bool $value) {
        $this->modified = $value;
    }

    public function modified(): bool {
        return $this->modified;
    } 
}