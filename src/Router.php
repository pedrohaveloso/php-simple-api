<?php

namespace SimpleApi;

class Router
{
  protected $routes = [];

  public function create(
    string $method, // Método HTTP.
    string $path, // URL/rota.
    callable $callback // Função executada nessa rota.
  ) {
    $this->routes[$method][$path] = $callback;
  }

  public function init()
  {
    // Colocamos o content-type da resposta para JSON.
    header('Content-Type: application/json; charset=utf-8');

    $httpMethod = $_SERVER["REQUEST_METHOD"];

    // O método atual existe em nossas rotas?
    if (isset($this->routes[$httpMethod])) {

      // Percore as rotas com o método atual:
      foreach ($this->routes[$httpMethod] as $path => $callback) {

        // Se a rota atual existir, retorna a função...
        if ($path === $_SERVER["REQUEST_URI"]) {
          return $callback();
        }
      }
    }

    // Caso não exista a rota/método atual: 
    http_response_code(404);
    return;
  }
}
