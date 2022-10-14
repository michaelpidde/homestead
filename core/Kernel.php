<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

final class Kernel {
    private string $clientDir = '';
    private string $clientNamespace = '';
    private Config $config;
    private array $routes = [];

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
        $this->routes = $parseResult->routes();
        
        $request = new Request();

        if(self::isRouteMatched($request, $this->routes)) {
            $match = $this->routes[$request->path()];
            $controllerClass = $match->controller();
            $controllerMethod = $match->controllerMethod();
            $controller = new $controllerClass($this->clientDir . DIRECTORY_SEPARATOR . $this->config->viewDir());
            $controller->$controllerMethod();
        }

        if($this->config->debugEnabled()) {
            $time = round(microtime(true) - $start, 4);
            Debug::inject($time, $parseResult->warnings());
        }
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

    private static function isRouteMatched(Request $request, array $routes): bool {
        foreach($routes as $key => $route) {
            if($request->path() == $key) {
                if($route->method() == $route->method()) {
                    return true;
                }
            }
        }
        return false;
    }
}

class KernelException extends Exception {}