<?php declare(strict_types=1);

require_once __DIR__ . '\..\core\autoload.php';

use Homestead\Core\Database;
use Homestead\Core\Kernel;
use Homestead\Core\Session;
use Clozerwoods\Service\AuthenticationService;

$kernel = new Kernel(__DIR__ . '\..\app', 'Clozerwoods');
$kernel->setAuthenticationHandler(new AuthenticationService(new Database(), Session::getInstance()));
$kernel->handle();