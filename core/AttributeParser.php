<?php declare(strict_types=1);

namespace Homestead\Core;

use \ReflectionMethod;

final class AttributeParser {
    static function parseControllerAttributes(string $namespace): ControllerAttributeParseResult {
        $routes = [];
        $warnings = [];
        foreach(get_declared_classes() as $class) {
            $parts = explode('\\', $class);
            if($parts[0] != $namespace) {
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
        $routes = [];
        $warnings = [];
        foreach(get_class_methods($class) as $method) {
            $ref = new ReflectionMethod($class, $method);
            foreach($ref->getAttributes() as $attr) {
                $attrClass = $attr->getName();
                if(!class_exists($attrClass)) {
                    $warnings['Attribute Class Not Found'] = "Attribute class $attrClass not found.";
                    continue;
                }
                $route = $attr->newInstance();
                $route->_controller($class);
                $route->_controllerMethod($method);
                $routes[$route->path()] = $route;
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