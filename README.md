# Fast 404

Quickly terminate an HTTP request with a `404 Not Found` response for static resources.

[![Latest Stable Version](https://poser.pugx.org/phpwatch/fast404/v)](https://packagist.org/packages/phpwatch/fast404) ![CI](https://github.com/PHPWatch/Fast404/workflows/CI/badge.svg?branch=master) [![Total Downloads](https://poser.pugx.org/phpwatch/fast404/downloads)](https://packagist.org/packages/phpwatch/fast404) [![License](https://poser.pugx.org/phpwatch/fast404/license)](https://github/phpwatch/fast404)

`phpwatch/fast404` is a library/middleware that you can to quickly terminate an HTTP request with a `404 Not Found` response.

The use case is a framework that handles all HTTP requests via a router, and returning a quick 404 message to static resources such as `.jpg` or `.png`. This prevents the framework from initializing rendering engines, database connections, etc to serve these types of requests. 

It is common that PHP frameworks use a router to handle incoming HTTP requests. Web server forwards all requests to the PHP framework (often to the `index.php` file). This has the side-effect of static resources such as `.jpg` or `.png` requests being routed to the PHP framework as well. 

Frameworks can generate a nice 404 Not Found error page, it is a waste of resources to generate nice error pages for images, videos, and other embeded content that are not the main URL the user accessed. 

This package comes with configurable but sensible defaults, and when added as a middleware, it is executed early in the request, and short-circuits the rest of the bootstrap process and returns a quick and dirty "Not Found" page with the proper HTTP header. This can reduce the overhead by not having to connect to the database or fire up a rendering engine.

Note that the rest of the execution will be terminated with a PHP `die()` call. If you want to log the error messages via the framework events or other middleware, this package is not for you. 

### Installation

```bash
composer require phpwatch/fast404
```

### Usage

You need to execute the provided middleware within your framework. 
Here is an example for Slim v3:

**1. Add `Fast404Middleware` class to the Container**

```php
<?php
$container[Fast404Middleware::class] = static function (Container $container) {  
  return new Fast404Middleware();  
};
```

**2. Use the middleware in individual routes/groups, or for the whole application**

```php
<?php
use PHPWatch\Fast404\Fast404Middleware;

$app->add(Fast404Middleware::class); // For whole app
$app->get/users/{username},...)->add(Fast404Middleware::class); // Or, for individual routes
```


### Configuration

You can declare settings when the `Fast404Middleware` is instantiated:

```php
<?php
new Fast404Middleware(string $error_message = 'Not found', string $regex = null, ?string $exclude_regex = null)
```

 - `$error_message`: Text for the error message. You can use anything here, including HTML content. Note that this library does not set a `content-type` header.
 - `$regex`: A valid regular expression including the delimiters. The default (below) is fast-404 a list of pre-configured file extensions. 
 - `$exclude_regex`: A regular expression that is run if provided, and if it matches, the request is _allowed_. You can combine this regular expression as a negative lookahead/behind match in `$regex` too, but that would make the regular expression a bit difficult to read. 

**Default match**

By default, the following regular expression is used. It matches a list of pre-configured common extensions. 

```regex
/\.(?:js|css|jpg|jpeg|gif|png|ico|exe|bin|dmg)$/i
```

Configure it to your hearts desire; Just make sure that you absolutely don't want the framework to continue the requests for these types of extensions. 

## PSR-15 / PSR-7
As of now, there is a `__invoke` method that accepts PSR-7 `ServerRequestInterface` object. This makes this library immediately compatible with Slim v3. Proper PSR-15 support is in the works, and will be compatible with Slim v4 and any other compatible dispatcher. 

## Log 404 errors
This library immediately terminates the request with a configurable error message and an HTTP 404 error. There will be no logging. If you want your web server to log these same errors, it should not have handed over the request to the framework. 

## I really need 404 logging
No. Get out and implement this yourself. This is a 40 LOC package. This README is **four** times larger than the code.
