<?php declare(strict_types=1);

require_once __DIR__ . '\..\..\core\autoload.php';

$kernel = new Homestead\Core\Kernel('..\..\app', 'Clozerwoods');
return $kernel->handled();