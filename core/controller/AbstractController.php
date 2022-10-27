<?php declare(strict_types=1);

namespace Homestead\Core\Controller;

use Homestead\Core\Request;
use \Exception;

abstract class AbstractController {
    function __construct(
        private string $viewDir,
        protected Request $request,
        private array $routes,
        private $redirect,
    ) {}

    function render(string $view, object|array $model = null): void {
        $content = $this->renderPartial($view, $model);
        require_once $this->viewDir . DIRECTORY_SEPARATOR . 'layout.php';
    }

    function renderPartial(string $view, object|array $model = null): string {
        if(gettype($model) == 'array') {
            extract($model);
        }

        $fullViewPath = $this->viewDir . DIRECTORY_SEPARATOR . $view . '.php';
        if(!file_exists($fullViewPath)) {
            throw new Exception("View $view does not exist.");
        }

        ob_start();
        require_once $fullViewPath;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    function redirectToRoute(string $path) {
        // This must be redeclared as a local variable in order to call it as a function.
        $redirect = $this->redirect;
        $redirect($path);
    }
}