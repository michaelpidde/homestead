<?php declare(strict_types=1);

namespace Homestead\Core\Controller;

use Homestead\Core\AuthenticationInterface;
use Homestead\Core\Request;
use Homestead\Core\Attribute\Route;

class FormAuthenticationController {
    function __construct(
        private AuthenticationInterface $authenticationService,
        protected Request $request,
        private $redirect
    ) {}

    #[Route('loginaction', 'POST')]
    function loginAction() {
        $data = $this->request->data();
        $redirect = $this->redirect;

        if(!array_key_exists('username', $data) || !array_key_exists('password', $data)) {
            $redirect($this->authenticationService->loginRoute());
        }

        if(!$this->authenticationService->authenticate($data['username'], $data['password'])) {
            $redirect($this->authenticationService->loginRoute());
        }

        $redirect($this->authenticationService->postLoginRoute());
    }
}