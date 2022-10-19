<?php declare(strict_types=1);

namespace Homestead\Test;

use Homestead\Core\Attribute\Route;

final class AnnotatedClass {
    #[Route('')]
    function test() {}

    #[Groute('')]
    function test2() {}
}