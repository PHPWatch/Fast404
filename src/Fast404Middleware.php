<?php

namespace PHPWatch\Fast404;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use function http_response_code;
use function preg_match;
use function str_contains;

class Fast404Middleware {
    private string $error_message;
    private ?string $regex;
    private ?string $exclude_regex;

    private const string ALLOW_MIME = 'text/html';

    final public function __construct(string $error_message = 'Not found', ?string $regex = null, ?string $exclude_regex = null) {
        $this->error_message = $error_message;
        $this->regex = $regex ?? '/\.(?:js|css|jpg|jpeg|gif|png|webp|ico|avif|exe|bin|dmg|mp4)$/i';
        $this->exclude_regex = $exclude_regex;
    }

    final public function __invoke(ServerRequestInterface $request, ResponseInterface $response, ?callable $next = null): ResponseInterface {
        if ($this->isFast404($request)) {
            $this->terminate();
        }

        if ($next) {
            return $next($request, $response);
        }

        return $response;
    }

    public function isFast404(ServerRequestInterface $request): bool {
        $uri = $request->getUri()->getPath();
        return
            $this->regex
            && !str_contains($request->getHeaderLine('accept'), static::ALLOW_MIME)
            && preg_match($this->regex, $uri)
            && !(isset($this->exclude_regex) && preg_match($this->exclude_regex, $uri));
    }

    /**
     * Terminate the request with an HTTP 404 code. This method is mocked when tested.
     * @codeCoverageIgnore
     */
    protected function terminate(): never {
        http_response_code(404);
        die($this->error_message);
    }
}
