<?php

namespace PHPWatch\Fast404\Tests;

use PHPWatch\Fast404\Fast404Middleware;

class Fast404MiddlewareTest extends TestCaseBase {
    public function testInstance(): void {
        $instance = new Fast404Middleware();
        $this->assertIsCallable([$instance, 'isFast404']);
    }

    public function testTriggersForMimes(): void {
        $instance = new Fast404Middleware();
        $this->assertTrue($instance->isFast404($this->getRequest('/foo.jpg', 'text/plain')));
        $this->assertFalse($instance->isFast404($this->getRequest('/foo.jpg', 'text/html')));
    }

    public function testTriggersForExtension(): void {
        $instance = new Fast404Middleware();
        $this->assertTrue($instance->isFast404($this->getRequest('/foo.jpg', 'image/jpeg')));
        $this->assertFalse($instance->isFast404($this->getRequest('/foojpg', 'image/jpeg')));
    }

    public function testTriggersWithExclusionPattern(): void {
        $instance = new Fast404Middleware('Not Found', null, '/jpg/i');
        $this->assertFalse($instance->isFast404($this->getRequest('/foo.jpg', 'image/jpeg')));
        $this->assertTrue($instance->isFast404($this->getRequest('/foo.png', 'image/jpeg')));
        $this->assertFalse($instance->isFast404($this->getRequest('/foojpg', 'image/jpeg')));
    }
}
