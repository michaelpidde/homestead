<?php declare(strict_types=1);

namespace Homestead\Core;

use Homestead\Core\Controller\FormAuthenticationController;
use \Exception;
use \RecursiveDirectoryIterator;
use \RecursiveIteratorIterator;

final class Kernel {
    const CACHE_DIR = '.cache';
    private string $clientDir = '';
    private string $clientNamespace = '';
    private Config $config;
    private array $routes = [];
    private array $warnings = [];
    private float $start;
    private ?AuthenticationInterface $authHandler = null;

    function __construct(string $clientDir, string $clientNamespace) {
        $this->clientDir = $clientDir;
        $this->clientNamespace = $clientNamespace;

        $this->start = microtime(true);

        if(!is_dir($clientDir)) {
            throw new KernelException("Client directory $clientDir does not exist.");
        }

        $this->config = SettingsParser::createConfig($this->clientDir);
        self::autoloadClientNamespace($this->clientDir, $this->clientNamespace);
        $fullControllerDir = $this->clientDir . DIRECTORY_SEPARATOR . $this->config->controllerDir();
        self::importAllControllers($fullControllerDir);

        if($this->config->authenticationEnabled()) {
            // TODO: Get rid of this jank somehow
            require 'Controller\\FormAuthenticationController.php';
            // Load our own controller(s) first
            $this->parseAttributesInNamespace(__NAMESPACE__ . '\Controller');
        }

        // $routeCacheDir = __DIR__ . DIRECTORY_SEPARATOR . self::CACHE_DIR;
        // $routeCacheFile = $routeCacheDir . DIRECTORY_SEPARATOR . 'routes.php';
        // if(!file_exists($routeCacheFile)) {
            $this->parseAttributesInNamespace($this->clientNamespace . '\Controller');
        //     if(empty($this->warnings)) {
        //         if(!is_dir($routeCacheDir)) {
        //             mkdir($routeCacheDir);
        //         }
        //         $handle = fopen($routeCacheFile, 'w');
        //         fwrite($handle, json_encode($this->routes));
        //         fclose($handle);
        //     }
        // } else {
        //     $this->routes = json_decode(file_get_contents($routeCacheFile), true);
        // }
    }

    function setAuthenticationHandler(AuthenticationInterface $authHandler): void {
        $this->authHandler = $authHandler;
    }

    function handle(): void {
        $request = new Request();
        $matchedRoute = false;
        $controller = '';
        $controllerMethod = '';

        if(self::isClientRouteMatched($request->path(), $this->routes)) {
            $matchedRoute = true;
            $match = $this->routes[$request->path()];
            $isAuthenticationRequest = $match->controller() === FormAuthenticationController::class;

            // Tie into authentication system
            if($match->authorize() || $isAuthenticationRequest) {
                if($this->authHandler == null) {
                    throw new KernelException('No authentication listener configured.');
                }
                $handler = new $this->authHandler(new Database(), Session::getInstance());

                if($isAuthenticationRequest) {
                    $controllerClass = $match->controller();
                    $controllerMethod = $match->controllerMethod();
                    $redirect = $this->redirectToRoute(...);
                    $controller = new $controllerClass($handler, $request, $redirect);
                } else {
                    if(!$handler->isAuthenticated()) {
                        $this->redirectToRoute($handler->loginRoute());
                    }
                }
            }

            if($controller === '') {
                // Load client controller
                $controllerClass = $match->controller();
                $controllerMethod = $match->controllerMethod();
                $redirect = $this->redirectToRoute(...);
                $controller = new $controllerClass(
                    $this->clientDir . DIRECTORY_SEPARATOR . $this->config->viewDir(),
                    $request,
                    $this->routes,
                    $redirect
                );
            }

            $controller->$controllerMethod();
        }

        if($this->config->debugEnabled()) {
            $time = round(microtime(true) - $this->start, 4);
            Debug::inject($time, $this->warnings);
        }
    }

    private function parseAttributesInNamespace(string $namespace): void {
        $parseResult = AttributeParser::parseControllerAttributes($namespace);
        $this->routes = array_merge($this->routes, $parseResult->routes());
        $this->warnings = array_merge($this->warnings, $parseResult->warnings());
    }

    private function redirectToRoute(string $path) {
        if(!array_key_exists($path, $this->routes)) {
            throw new Exception("Could not find route '{$path}' to redirect to.");
        }

        header("location: /$path");
        die;
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
}

class KernelException extends Exception {}