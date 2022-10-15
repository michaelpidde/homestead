<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller;
use Homestead\Core\Attribute\Route;

// Temporary garbage controller to test error scenarios

class Groute extends Controller {
    #[Route('')]
    function home() {
        echo 'Groute Home';
    }

    #[Floute('')]
    function dome() {
        echo 'Dome';
    }
}