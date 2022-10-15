<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

final class Kernel {
    private string $clientDir = '';
    private string $clientNamespace = '';
    private bool $handled = true;
    private Config $config;

    function __construct(string $clientDir, string $clientNamespace) {
        $this->clientDir = $clientDir;
        $this->clientNamespace = $clientNamespace;

        $start = microtime(true);

        if(!is_dir($clientDir)) {
            throw new KernelException("Client directory $clientDir does not exist.");
        }

        $this->config = SettingsParser::createConfig($this->clientDir);
        self::autoloadClientNamespace($this->clientDir, $this->clientNamespace);
        $fullControllerDir = $this->clientDir . DIRECTORY_SEPARATOR . $this->config->controllerDir();
        self::importAllControllers($fullControllerDir);

        // TODO: Cache this somehow if non-debug so we don't need to parse attributes every page load
        $parseResult = AttributeParser::parseControllerAttributes($this->clientNamespace);
        $routes = $parseResult->routes();
        
        $request = new Request();
        $matchedRoute = false;

        if(self::isClientRouteMatched($request->path(), $routes)) {
            $matchedRoute = true;
            $match = $routes[$request->path()];
            $controllerClass = $match->controller();
            $controllerMethod = $match->controllerMethod();
            $controller = new $controllerClass($this->clientDir . DIRECTORY_SEPARATOR . $this->config->viewDir());
            $controller->$controllerMethod();
        }

        if(!$matchedRoute && self::isStaticPath($request->path())) {
            $this->handled = false;
            return;
        }

        if($this->config->debugEnabled()) {
            $time = round(microtime(true) - $start, 4);
            Debug::inject($time, $parseResult->warnings());
        }
    }

    /*
     * This can be used to tell the server that the request should be handled by it rather than by this kernel.
     * This is used because our application is running as a request proxy. If a static asset is detected, defer
     * should indicate that the kernel is not handling the request.
     */
    public function handled(): bool {
        return $this->handled;
    }

    private static function autoloadClientNamespace(string $clientDir, string $namespace): void {
        spl_autoload_register(function($class) use ($clientDir, $namespace) {
            $parts = explode('\\', $class);
            if($parts[0] == $namespace) {
                array_shift($parts);
                $class = implode('\\', $parts);
            }
            $path = $clientDir . DIRECTORY_SEPARATOR . $class . '.php';
            if(file_exists($path)) {
                require_once $path;
            }
        });
    }

    private static function importAllControllers(string $controllerDir): void {
        if(!is_dir($controllerDir)) {
            throw new KernelException("Controller directory '{$controllerDir}' not found.");
        }

        $dirIterator = new RecursiveDirectoryIterator($controllerDir);
        $iterator = new RecursiveIteratorIterator($dirIterator);
        foreach($iterator as $file) {
            if($file->getExtension() === 'php') {
                include $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename();
            }
        }
    }

    private static function isClientRouteMatched(string $path, array $routes): bool {
        foreach($routes as $key => $route) {
            if($path == $key) {
                if($route->method() == $route->method()) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function isStaticPath(string $path) {
        return preg_match('/\.(?:png|jpg|gif|js|css)$/', $path);
    }
}

class KernelException extends Exception {}