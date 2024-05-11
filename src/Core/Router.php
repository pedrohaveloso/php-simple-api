<?php

namespace SimpleApi\Core;

use Closure;
use Exception;

class Router
{
  public function __construct(
    public $contentType = "application/json",
    public $charset = "utf-8",
  ) {
  }

  private $routes = [];

  private function create(
    string $httpMethod,
    string $path,
    Closure|array $callback
  ) {
    if (is_array($callback)) {
      $class = $callback[0];
      $method = $callback[1];

      if (!method_exists($class, $method)) {
        throw new Exception("Method \"{$method}\" not found in \"{$class}\".");
      }

      $callback = fn (Request $request) => $class::$method($request);
    }

    $this->routes[$httpMethod][$path] = $callback;
  }

  public function get(string $path, Closure|array $callback)
  {
    $this->create("GET", $path, $callback);
  }

  public function post(string $path, Closure|array $callback)
  {
    $this->create("POST", $path, $callback);
  }

  public function init(): never
  {
    header("Content-Type: {$this->contentType}; charset={$this->charset}");

    $callback =
      $this->routes[$_SERVER["REQUEST_METHOD"]][$_SERVER["REQUEST_URI"]]
      ?? null;

    if ($callback !== null) {
      $request = new Request(
        $_SERVER["REQUEST_METHOD"],
        $_SERVER["REQUEST_URI"],
        json_decode(file_get_contents('php://input'), true),
        $_GET
      );

      switch (($response = $callback($request)) instanceof Response) {
        case true:
          http_response_code($response->statusCode);
          echo $response->body;
          break;

        case false:
          throw new Exception("Response must be an instance of Response");
          break;
      }
    }

    if ($callback === null) {
      http_response_code(404);
    }

    exit;
  }
}
