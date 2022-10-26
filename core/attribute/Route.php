<?php declare(strict_types=1);

namespace Homestead\Core\Attribute;

use \Attribute;

#[Attribute]
final class Route {
    private string $controller = '';
    private string $controllerMethod = '';
    private ?bool $authorize = null;

    function __construct(
        private string $path,
        private string $method = 'GET'
    ) {}

    function path(): string {
        return $this->path;
    }

    function method(): string {
        return $this->method;
    }

    function authorize(): bool {
        return $this->authorize ?? false;
    }

    function _authorize(bool $value): void {
        if($this->authorize == null) {
            $this->authorize = $value;
        } else {
            throw new Exception('Cannot reset route\'s authorization.');
        }
    }

    function controller(): string {
        return $this->controller;
    }

    function _controller(string $value): void {
        if($this->controller == '') {
            $this->controller = $value;
        } else {
            throw new Exception('Cannot reset route\'s controller.');
        }
    }

    function controllerMethod(): string {
        return $this->controllerMethod;
    }

    function _controllerMethod(string $value): void {
        if($this->controllerMethod == '') {
            $this->controllerMethod = $value;
        } else {
            throw new Exception('Cannot reset route\'s controller method.');
        }
    }
}