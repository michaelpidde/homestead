<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Attribute\Route;

// Temporary garbage controller to test error scenarios

class Groute extends AbstractController {
    #[Route('groute')]
    function home() {
        echo 'Groute Home';
    }

    #[Floute('')]
    function dome() {
        echo 'Dome';
    }
}