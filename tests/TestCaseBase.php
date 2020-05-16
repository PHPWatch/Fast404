<?php


namespace PHPWatch\Fast404\Tests;


use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TestCaseBase extends TestCase {
    protected function getRequest(string $uri, string $accept_header): ServerRequestInterface {
        return new ServerRequest('GET', $uri, ['accept' => $accept_header]);
    }

    protected function getResponse(): ResponseInterface {
        return new Response();
    }
}
