<?php declare(strict_types=1);

namespace Homestead\Core;

use \ReflectionClass;
use \ReflectionMethod;
use Homestead\Core\Attribute\Route as RouteAttribute;
use Homestead\Core\Attribute\Authorize as AuthorizeAttribute;
use Homestead\Core\Attribute\Parameter as ParameterAttribute;

final class AttributeParser {
    public static function parseControllerAttributes(string $namespace): ControllerAttributeParseResult {
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
            $parameters = [];
            $route = null;
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
                }
                if($attrClass === ParameterAttribute::class) {
                    $parameters[] = $attr->newInstance();
                }
                if($attrClass === AuthorizeAttribute::class) {
                    $authorize[] = "$class::$methodName";
                }
            }

            if($route === null) {
                continue;
            }

            $pattern = $route->path();
            foreach($parameters as $parameter) {
                $pattern = str_replace("{{$parameter->name()}}", "({$parameter->pattern()})", $pattern);
            }
            $route->_pattern($pattern);
            $routes[] = $route;
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
            foreach($existingRoutes as $existingRoute) {
                if($path == $existingRoute->path()) {
                    $warnings['Duplicate Route Definition'] = "Route '{$path}' is duplicated in {$existingRoute->controller()} and {$route->controller()}. The latter will take precedence.";
                }
            }
        }
        return $warnings;
    }
}