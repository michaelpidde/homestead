<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

class Request {
    protected string $path;
    protected string $method;
    protected array $data = [];

    function __construct() {
        $this->parseServerVars($_SERVER);
    }

    private function parseServerVars(array $vars): void {
        if(!array_key_exists('REQUEST_URI', $vars)) {
            throw new RequestException('Could not find REQUEST_URI.');
        }
        $this->path = ltrim($vars['REQUEST_URI'], '/');

        if(!array_key_exists('REQUEST_METHOD', $vars)) {
            throw new RequestException('Could not find REQUEST_METHOD.');
        }
        $this->method = $vars['REQUEST_METHOD'];
    }

    function path(): string {
        return $this->path;
    }

    function method(): string {
        return $this->method;
    }

    function _data(string $key, string $value) {
        $this->data[$key] = $value;
    }

    function data(): array {
        $this->data = array_merge($this->data, $_REQUEST);
        $this->data = array_merge($this->data, $_GET);
        $this->data = array_merge($this->data, $_POST);
        return $this->data;
    }

    function get(string $key): mixed {
        if(array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }
}

class RequestException extends Exception {}
