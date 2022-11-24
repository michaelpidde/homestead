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
    private string $pattern = '';

    public function __construct(
        private string $path,
        private string $method = self::DEFAULT_METHOD
    ) {}

    public function path(): string {
        return $this->path;
    }

    public function method(): string {
        return $this->method;
    }

    public function authorize(): bool {
        return $this->authorize ?? false;
    }

    public function _authorize(bool $value): void {
        if($this->authorize == null) {
            $this->authorize = $value;
        } else {
            throw new Exception('Cannot reset route\'s authorization.');
        }
    }

    public function controller(): string {
        return $this->controller;
    }

    public function _controller(string $value): void {
        if($this->controller == '') {
            $this->controller = $value;
        } else {
            throw new Exception('Cannot reset route\'s controller.');
        }
    }

    public function controllerMethod(): string {
        return $this->controllerMethod;
    }

    public function _controllerMethod(string $value): void {
        if($this->controllerMethod == '') {
            $this->controllerMethod = $value;
        } else {
            throw new Exception('Cannot reset route\'s controller method.');
        }
    }

    public function pattern(): string {
        return $this->pattern;
    }

    public function _pattern(string $value) {
        if($this->pattern == '') {
            $this->pattern = $value;
        } else {
            throw new Exception('Cannot reset route\'s pattern.');
        }
    }

    public function jsonSerialize(): array {
        return [
            'controller' => $this->controller,
            'controllerMethod' => $this->controllerMethod,
            'authorize' => $this->authorize,
            'path' => $this->path,
            'method' => $this->method,
            'pattern' => $this->pattern,
        ];
    }

    public static function cast(object $untyped): ?self {
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