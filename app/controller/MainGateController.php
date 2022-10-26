<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Attribute\Authorize;
use Homestead\Core\Attribute\Route;
use Clozerwoods\Model\MainGateViewModel;

class MainGateController extends AbstractController {
    #[Route('maingate/login')]
    function login() {
        $this->render('maingate/login', new MainGateViewModel());
    }

    #[Authorize]
    #[Route('maingate/dashboard')]
    function dashboard() {
        echo 'Dashboard';
    }
}