<?php declare(strict_types=1);

namespace Homestead\Core;

interface PageViewModel {
    public function title(): string;
    public function nav(): array;
}