<?php declare(strict_types=1);

namespace Homestead\Core;

interface PageViewModel {
    function title(): string;
    function nav(): array;
}