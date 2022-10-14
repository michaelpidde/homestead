<?php declare(strict_types=1);

require('..\autoload.php');

use PHPUnit\Framework\TestCase;
use Homestead\Core\ControllerAttributeParseResult;

final class ControllerAttributeParseResultTest extends TestCase {
    function testCreation() {
        $result = new ControllerAttributeParseResult(['route'], ['warning']);
        $this->assertEquals($result->routes(), ['route']);
        $this->assertEquals($result->warnings(), ['warning']);
    }
}