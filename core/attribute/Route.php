<?php declare(strict_types=1);

namespace Homestead\Core\Attribute;

use \Attribute;
use \JsonSerializable;

#[Attribute]
final class Route implements JsonSerializable {
    const DEFAULT_METHOD = 'GET';
    private string $controller = '';
    private string $controllerMethod = '';
    private ?bool $authorize = null;

    function __construct(
        private string $path,
        private string $method = self::DEFAULT_METHOD
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

    function jsonSerialize(): array {
        return [
            'controller' => $this->controller,
            'controllerMethod' => $this->controllerMethod,
            'authorize' => $this->authorize,
            'path' => $this->path,
            'method' => $this->method,
        ];
    }

    static function cast(object $untyped): ?self {
        if(!property_exists($untyped, 'path')) {
            return null;
        }
        $instance = new self($untyped->path, $untyped->method ?? self::DEFAULT_METHOD);
        $instance->_authorize($untyped->authorize ?? false);
        $instance->_controller($untyped->controller ?? '');
        $instance->_controllerMethod($untyped->controllerMethod ?? '');
        return $instance;
    }
}