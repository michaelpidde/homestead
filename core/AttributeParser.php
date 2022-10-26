<?php declare(strict_types=1);

namespace Homestead\Core;

use \ReflectionClass;
use \ReflectionMethod;
use Homestead\Core\Attribute\Route as RouteAttribute;
use Homestead\Core\Attribute\Authorize as AuthorizeAttribute;

final class AttributeParser {
    static function parseControllerAttributes(string $namespace): ControllerAttributeParseResult {
        $authorize = [];
        $routes = [];
        $warnings = [];
        foreach(get_declared_classes() as $class) {
            if(strpos($class, $namespace) !== 0) {
                continue;
            }
            $parseResult = self::parseMethodAttributes($class);
            $warnings = array_merge($warnings, $parseResult->warnings());
            $warnings = array_merge($warnings, self::checkForDuplicateRoutes($parseResult->routes(), $routes));
            // A duplicate route will overwrite a previously parsed route. This should not terminate the kernel.
            $routes = array_merge($routes, $parseResult->routes());
        }

        return new ControllerAttributeParseResult($routes, $warnings);
    }

    private static function parseMethodAttributes(string $class): ControllerAttributeParseResult {
        $authorize = [];
        $routes = [];
        $warnings = [];
        $refClass = new ReflectionClass($class);

        foreach($refClass->getMethods() as $method) {
            if($method->class !== $class) {
                continue;
            }
            $methodName = $method->getName();
            $ref = new ReflectionMethod($class, $methodName);
            foreach($ref->getAttributes() as $attr) {
                $attrClass = $attr->getName();
                if(!class_exists($attrClass)) {
                    $warnings['Attribute Class Not Found'] = "Attribute class $attrClass not found.";
                    continue;
                }
                if($attrClass === RouteAttribute::class) {
                    $route = $attr->newInstance();
                    $route->_controller($class);
                    $route->_controllerMethod($methodName);
                    $routes[$route->path()] = $route;
                }
                if($attrClass === AuthorizeAttribute::class) {
                    $authorize[] = "$class::$methodName";
                }
            }
        }
        
        foreach($routes as $route) {
            if(in_array("{$route->controller()}::{$route->controllerMethod()}", $authorize)) {
                $route->_authorize(true);
            }
        }

        return new ControllerAttributeParseResult($routes, $warnings);
    }

    private static function checkForDuplicateRoutes(array $newRoutes, array $existingRoutes): array {
        $warnings = [];
        foreach($newRoutes as $route) {
            $path = $route->path();
            if(array_key_exists($path, $existingRoutes)) {
                $warnings['Duplicate Route Definition'] = "Route '{$path}' is duplicated in {$existingRoutes[$path]->controller()} and {$route->controller()}. The latter will take precedence.";
            }
        }
        return $warnings;
    }
}