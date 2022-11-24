<?php declare(strict_types=1);

namespace Homestead\Core\Controller;

use Homestead\Core\Debug;
use Homestead\Core\Renderer;
use Homestead\Core\Request;
use \Exception;

abstract class AbstractController {
    public function __construct(
        protected Request $request,
        private array $routes,
        private $redirect,
    ) {}

    public function render(string $view, object|array $model = null): void {
        Renderer::render($view, $model);
    }

    public function redirectToRoute(string $path) {
        // This must be redeclared as a local variable in order to call it as a function.
        $redirect = $this->redirect;
        $redirect($path);
    }
}