<?php declare(strict_types=1);

namespace Homestead\Core;

use ReflectionObject;
use ReflectionProperty;
use Homestead\Core\Attribute\Route;

final class Debug {
    private const PUBLIC = 'public';
    private const PROTECTED = 'protected';
    private const PRIVATE = 'private';

    private static $enabled = false;
    private static $views = [];
    private static ?Route $route = null;

    public static function enable() {
        self::$enabled = true;
    }

    public static function view(string $name, array|object $model): void {
        self::$views[] = [
            'name' => $name,
            'model' => $model,
        ];
    }

    public static function route(Route $route): void {
        self::$route = $route;
    }

    public static function inject(float $seconds, array $warnings): void {
        if(!self::$enabled) {
            return;
        }

        $content = <<<STR
            <section id="homestead-debug">
                <div id="homestead-debug-toggle">&searr;</div>
                <div id="homestead-debug-content">
                <p>{$seconds}s</p>
        STR;

        if(count($warnings)) {
            $content .= '<b>Warnings</b><br>';
        }
        foreach($warnings as $warning) {
            $content .= "$warning<br>";
        }

        if(self::$route) {
            $route = self::$route;
            $content .= <<<STR
                <p>
                    <b>Matched Route</b><br>
                    {$route->method()} - {$route->path()}<br>
                    {$route->controller()}::{$route->controllerMethod()}
                </p>
            STR;
        }

        foreach(self::$views as $view) {
            if(gettype($view['model']) === 'object') {
                $model = self::renderObject($view['model']);
            } else {
                $model = var_export($view['model'], true);
            }
            $content .= <<<STR
                <p>
                    <b>View</b><br>
                    Name: {$view['name']}<br>
                    Model: {$model}
                </p>
            STR;
        }

        $content .= '</div></section>';
        $content .= self::assets();

        echo $content;
    }

    public static function renderObject(object $obj): string {
        $class = get_class($obj);
        $output = <<<STR
            <div class="render-object">
                <div class="object-class">{$class}</div>
        STR;
        $objectRef = new ReflectionObject($obj);
        foreach($objectRef->getProperties() as $property) {
            $accessibility = self::accessibility($property);
            $property->setAccessible(true);
            $value = $property->getValue($obj);
            $type = gettype($value);

            $output .= <<<STR
                <div class="property">
                    <div class="identifier">
                        <span class="value-toggle">+</span>
                        <span class="name access-{$accessibility}">{$property->getName()}</span>
                        <span class="type">&lt;{$type}&gt;</span>
                    </div>
                    <div class="value">
            STR;

            $output .= self::renderValue($type, $value);
            
            // .property-value
            // .property
            $output .= <<<STR
                    </div>
                </div>
            STR;
        }

        // .render-object
        $output .= '</div>';
        return $output;
    }

    public static function renderValue(string $type, mixed $value): string {
        if($type == 'array') {
            if(count($value) == 0) {
                return '[]';
            }
            $output = '';
            foreach($value as $element) {
                $output .= self::renderValue(gettype($element), $element);
            }
            return $output;
        } else if($type == 'object') {
            return self::renderObject($value);
        } else {
            if($type == 'string') {
                return htmlentities(str_replace("\n", '\n', $value));
            }
            if($type == 'NULL') {
                return '<i>null</i>';
            }
            if($type == 'boolean') {
                return var_export($value, true);
            }
        }
        return (string)$value;
    }

    public static function accessibility(ReflectionProperty $property): string {
        if($property->isPublic()) {
            return self::PUBLIC;
        }
        if($property->isProtected()) {
            return self::PROTECTED;
        }
        if($property->isPrivate()) {
            return self::PRIVATE;
        }
        return '';
    }

    public static function assets(): string {
        $assets = __DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $css = file_get_contents($assets . 'debug.css');
        $js = file_get_contents($assets . 'debug.js');
        return <<<STR
            <style type="text/css">
                {$css}
            </style>
            <script type="text/javascript">
                {$js}
            </script>
        STR;
    }
}