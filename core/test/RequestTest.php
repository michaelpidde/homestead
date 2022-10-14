<?php declare(strict_types=1);

require('..\autoload.php');

use PHPUnit\Framework\TestCase;
use Homestead\Core\Request;
use Homestead\Core\RequestException;

final class RequestTest extends TestCase {
    function setUp(): void {
        $_SERVER = [];
    }

    function testCreation() {
        $_SERVER['REQUEST_URI'] = 'uri';
        $_SERVER['REQUEST_METHOD'] = 'method';
        $request = new Request();
        $this->assertEquals($request->path(), 'uri');
        $this->assertEquals($request->method(), 'method');
    }
    
    function testNoRequestUri() {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Could not find REQUEST_URI.');
        $request = new Request();
    }

    function testNoRequestMethod() {
        $_SERVER['REQUEST_URI'] = '/';
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Could not find REQUEST_METHOD.');
        $request = new Request();
    }
}