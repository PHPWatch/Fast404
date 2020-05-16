<?php

namespace PHPWatch\Fast404;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use function http_response_code;
use function preg_match;
use function strpos;

class Fast404Middleware {
    private string $error_message;
    private ?string $regex;
    private ?string $exclude_regex;

    private const ALLOW_MIME = 'text/html';

    final public function __construct(string $error_message = 'Not found', string $regex = null, ?string $exclude_regex = null) {
        $this->error_message = $error_message;
        $this->regex = $regex ?? '/\.(?:js|css|jpg|jpeg|gif|png|webp|ico|exe|bin|dmg)$/i';
        $this->exclude_regex = $exclude_regex;
    }

    final public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null): ResponseInterface {
        if ($this->isFast404($request)) {
            $this->terminate();
        }

        if ($next) {
            return $next($request, $response);
        }
    }

    public function isFast404(ServerRequestInterface $request): bool {
        $uri = $request->getUri()->getPath();
        return
            $this->regex
            && strpos($request->getHeaderLine('accept'), static::ALLOW_MIME) === false
            && preg_match($this->regex, $uri)
            && !(isset($this->exclude_regex) && preg_match($this->exclude_regex, $uri));
    }

    protected function terminate(): void {
        http_response_code(404);
        die($this->error_message);
    }
}
