<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

class Renderer {
    private static string $viewDir = '';

    public static function _viewDir(string $viewDir) {
        self::$viewDir = $viewDir;
    }

    public static function render(string $view, object|array $model = null): void {
        $content = self::renderPartial($view, $model);
        require_once self::$viewDir . DIRECTORY_SEPARATOR . 'layout.php';
    }

    public static function renderPartial(string $view, object|array $model = null): string {
        if(gettype($model) == 'array') {
            extract($model);
        }

        $fullViewPath = self::$viewDir . DIRECTORY_SEPARATOR . $view . '.php';
        if(!file_exists($fullViewPath)) {
            throw new Exception("View $view does not exist.");
        }

        Debug::view($view, $model);

        ob_start();
        require_once $fullViewPath;
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}