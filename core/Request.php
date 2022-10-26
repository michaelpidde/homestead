<?php declare(strict_types=1);

namespace Homestead\Core;

use \Exception;

class Request {
    protected string $path;
    protected string $method;

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

    function data(): array {
        $data = [];
        $data = array_merge($data, $_REQUEST);
        $data = array_merge($data, $_GET);
        $data = array_merge($data, $_POST);
        return $data;
    }
}

class RequestException extends Exception {}
