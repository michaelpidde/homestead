<?php declare(strict_types=1);

namespace Homestead\Core;

use Homestead\Core\Attribute\Route;
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
    private ?Request $request = null;
    private ?Route $matchedRoute = null;
    private ?AuthenticationInterface $authHandler = null;

    public function __construct(string $clientDir, string $clientNamespace) {
        $this->clientDir = $clientDir;
        $this->clientNamespace = $clientNamespace;

        date_default_timezone_set('America/Chicago');

        $this->start = microtime(true);

        if(!is_dir($clientDir)) {
            throw new KernelException("Client directory $clientDir does not exist.");
        }

        $this->config = SettingsParser::createConfig($this->clientDir);
        Logger::setLevel($this->config->logLevel());
        Logger::cull();

        self::autoloadClientNamespace($this->clientDir, $this->clientNamespace);
        $fullControllerDir = $this->clientDir . DIRECTORY_SEPARATOR . $this->config->controllerDir();
        self::importAllControllers($fullControllerDir);

        if($this->config->authenticationEnabled()) {
            // Load our own controller(s) first
            self::importAllControllers(__DIR__ . DIRECTORY_SEPARATOR . 'Controller');
            $this->parseAttributesInNamespace(__NAMESPACE__ . '\Controller');
        }

        Renderer::_viewDir($this->clientDir . DIRECTORY_SEPARATOR . $this->config->viewDir());

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
        //     $decoded = json_decode(file_get_contents($routeCacheFile));
        //     foreach($decoded as $untyped) {
        //         $typed = Route::cast($untyped);
        //         $this->routes[$typed->path()] = $typed;
        //     }
        //     Logger::info('Loaded routes from cache.');
        // }
    }

    public function setAuthenticationHandler(AuthenticationInterface $authHandler): void {
        $this->authHandler = $authHandler;
    }

    public function handle(): void {
        $this->request = new Request();
        $matchedRoute = false;
        $controller = '';
        $controllerMethod = '';

        if($this->matchClientRoute()) {
            $matchedRoute = true;
            Debug::route($this->matchedRoute);
            $isAuthenticationRequest = $this->matchedRoute->controller() === FormAuthenticationController::class;

            // Tie into authentication system
            if($this->matchedRoute->authorize() || $isAuthenticationRequest) {
                if($this->authHandler == null) {
                    throw new KernelException('No authentication listener configured.');
                }
                $handler = new $this->authHandler(new Database(), Session::getInstance());

                if($isAuthenticationRequest) {
                    $controllerClass = $this->matchedRoute->controller();
                    $controllerMethod = $this->matchedRoute->controllerMethod();
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
                $controllerClass = $this->matchedRoute->controller();
                $controllerMethod = $this->matchedRoute->controllerMethod();
                $redirect = $this->redirectToRoute(...);
                $controller = new $controllerClass(
                    $this->request,
                    $this->routes,
                    $redirect
                );
            }

            $controller->$controllerMethod();
        }

        if($this->config->debugEnabled()) {
            Debug::enable();
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
        $matched = false;
        foreach($this->routes as $route) {
            if($route->pattern() === $path) {
                $matched = true;
            }
        }
        if(!$matched) {
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
                require_once $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename();
            }
        }
    }

    private function matchClientRoute(): bool {
        foreach($this->routes as $route) {
            $patternToMatch = str_replace('/', '\/', $route->pattern());
            if(preg_match("/{$patternToMatch}/", $this->request->path(), $matches) != 1) {
                continue;
            }
            if($route->method() != $this->request->method()) {
                continue;
            }
            $this->matchedRoute = $route;
            if(count($matches) > 1) {
                preg_match('/{([A-Za-z_0-9]+)}/', $route->path(), $parameterNames);
                if(count($parameterNames) > 1) {
                    for($i = 1; $i < count($parameterNames); ++$i) {
                        $this->request->_data($parameterNames[$i], $matches[$i]);
                    }
                }
            }
            return true;
        }
        return false;
    }
}

class KernelException extends Exception {}