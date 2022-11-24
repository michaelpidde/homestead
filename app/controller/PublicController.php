<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Attribute\Route;
use Clozerwoods\ViewModel\HomeViewModel;

class PublicController extends AbstractController {
    #[Route('')]
    public function home() {
        $this->render('home', new HomeViewModel('Home', []));
    }
}