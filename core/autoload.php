<?php declare(strict_types=1);

spl_autoload_register(function($class) {
    $token = 'Homestead\Core';
    if(strpos($class, $token) === 0) {
        $class = str_replace($token, '', $class);
    }
    $path = __DIR__ . $class . '.php';
    if(file_exists($path)) {
        require_once $path;
    }
});