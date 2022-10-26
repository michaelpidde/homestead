<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Homestead\Core\Controller\AbstractController;
use Homestead\Core\Request;
use \Exception;

final class ConcreteController extends AbstractController {}

final class ViewModel {
    function thing() { return 'Hello'; }
}

final class ControllerTest extends TestCase {
    private AbstractController $controller;
    
    function setUp(): void {
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->controller = new ConcreteController('.', new Request(), [], function() {});
    }

    function testRender() {
        $this->markTestSkipped('broken... -_-');
        ob_start();
        $this->controller->render('view_for_array', ['thing' => 'Hello']);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<div>Hello</div>', $content);
    }

    function testRenderPartial_WithArray() {
        $content = $this->controller->renderPartial('view_for_array', ['thing' => 'Hello']);
        $this->assertEquals('Hello', $content);
    }

    function testRenderPartial_WithViewModel() {
        $content = $this->controller->renderPartial('view_for_view_model', new ViewModel());
        $this->assertEquals('Hello', $content);
    }

    function testRenderPartial_ViewNotFound() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('View does_not_exist does not exist.');
        $this->controller->renderPartial('does_not_exist');
    }
}