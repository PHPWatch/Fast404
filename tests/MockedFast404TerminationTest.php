<?php


namespace PHPWatch\Fast404\Tests;


use PHPWatch\Fast404\Fast404Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MockedFast404TerminationTest extends TestCaseBase {
    public function testTermination(): void {
        $instance = new class extends Fast404Middleware {
            protected const ALLOW_MIME = 'text/html';
            protected function terminate(): void {
                throw new \RuntimeException('Triggerred mocked method');
            }
        };

        $this->assertFalse($instance->isFast404($this->getRequest('foojpg', 'text/html')));
        $this->assertFalse($instance->isFast404($this->getRequest('foo.jpg', 'text/html')));
        $this->assertFalse($instance->isFast404($this->getRequest('foo.jpg', 'text/html')));
        $this->assertTrue($instance->isFast404($this->getRequest('foo.jpg', 'image/jpeg')));

        $this->expectExceptionMessage('Triggerred mocked method');
        $instance($this->getRequest('foo.jpg', 'image/jpeg'), $this->getResponse());
    }

    public function testCallsNextClosure(): void {
        $instance = new Fast404Middleware();
        $called = false;
        $next = static function(ServerRequestInterface $request, ResponseInterface $response) use (&$called): ResponseInterface {
            $called = true;
            return $response;
        };

        $instance($this->getRequest('foojpg', 'image/jpeg'), $this->getResponse());
        $this->assertFalse($called);

        $instance($this->getRequest('foojpg', 'image/jpeg'), $this->getResponse(), $next);
        $this->assertTrue($called);
    }
}
