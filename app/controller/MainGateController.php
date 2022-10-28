<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Attribute\Authorize;
use Homestead\Core\Attribute\Route;
use Homestead\Core\Request;
use Homestead\Core\Session;
use Clozerwoods\Model\MainGateViewModel;

class MainGateController extends AbstractController {
    function __construct(
        string $viewDir,
        Request $request,
        array $routes,
        $redirect,
    ) {
        parent::__construct($viewDir, $request, $routes, $redirect);
        Session::start();
    }

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