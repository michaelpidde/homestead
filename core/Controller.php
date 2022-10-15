<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

abstract class Controller {
    function __construct(
        private string $viewDir
    ) {}

    function render(string $view, object|array $model = null) {
        if(gettype($model) == 'array') {
            echo 'extracted';
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

        require_once $this->viewDir . DIRECTORY_SEPARATOR . 'layout.php';
    }
}