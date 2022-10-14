<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller;
use Homestead\Core\Attribute\Route;
use Clozerwoods\Model\HomeViewModel;

class PublicController extends Controller {
    #[Route('/')]
    function home() {
        $this->render('home', new HomeViewModel());
    }
}