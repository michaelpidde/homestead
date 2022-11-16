<?php declare(strict_types=1);

namespace Clozerwoods\Controller;

use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Attribute\Authorize;
use Homestead\Core\Attribute\Route;
use Homestead\Core\Attribute\Parameter;
use Homestead\Core\Request;
use Homestead\Core\Session;
use Clozerwoods\ViewModel\MainGate\BaseViewModel;
use Clozerwoods\ViewModel\MainGate\PageViewModel;
use Clozerwoods\Model\NavItem;
use Clozerwoods\Model\Page;
use Clozerwoods\Service\MainGateService;

class MainGateController extends AbstractController {
    private array $nav = [];
    
    function __construct(
        string $viewDir,
        Request $request,
        array $routes,
        $redirect,
    ) {
        parent::__construct($viewDir, $request, $routes, $redirect);
        Session::start();

        $this->nav = [
            new NavItem('maingate/pages', 'Pages'),
            new NavItem('maingate/galleries', 'Galleries'),
            new NavItem('maingate/media', 'Media Items'),
        ];
    }

    #[Route('maingate/login')]
    function login() {
        $this->render('maingate/login', new BaseViewModel('Main Gate - Login', $this->nav));
    }

    #[Authorize]
    #[Route('maingate/dashboard')]
    function dashboard() {
       $this->render('maingate/dashboard', new BaseViewModel('Main Gate - Dashboard', $this->nav));
    }

    #[Authorize]
    #[Route('maingate/pages')]
    function listPages() {
        $model = new PageViewModel('Main Gate - Pages', $this->nav);
        $service = new MainGateService();
        // $model->_modified(false);
        // $model->_pages([]);
        $this->render('maingate/pages', $model);
    }

    #[Authorize]
    #[Route('maingate/page/{id}')]
    #[Parameter('id', '\d+')]
    function editPage() {
        $model = new PageViewModel('Main Gate - Edit Page', $this->nav);
        $service = new MainGateService();
        // $model->_modified(false);
        // $page = $service->getPage(1);
        // $model->_selectedPage($page);
        $this->render('maingate/page', $model);
    }

    #[Authorize]
    #[Route('maingate/page/{id}', 'POST')]
    #[Parameter('id', '\d+')]
    function editPageAction() {
        // 
    }
}