<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Homestead\Core\Controller;
use \Exception;

final class ConcreteController extends Controller {}

final class ViewModel {
    function thing() { return 'Hello'; }
}

final class ControllerTest extends TestCase {
    function testRender() {
        $this->markTestSkipped('broken... -_-');
        $controller = new ConcreteController('.');
        ob_start();
        $controller->render('view_for_array', ['thing' => 'Hello']);
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<div>Hello</div>', $content);
    }

    function testRenderPartial_WithArray() {
        $controller = new ConcreteController('.');
        $content = $controller->renderPartial('view_for_array', ['thing' => 'Hello']);
        $this->assertEquals('Hello', $content);
    }

    function testRenderPartial_WithViewModel() {
        $controller = new ConcreteController('.');
        $content = $controller->renderPartial('view_for_view_model', new ViewModel());
        $this->assertEquals('Hello', $content);
    }

    function testRenderPartial_ViewNotFound() {
        $controller = new ConcreteController('.');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('View does_not_exist does not exist.');
        $controller->renderPartial('does_not_exist');
    }
}