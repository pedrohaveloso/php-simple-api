<?php

namespace SimpleApi;

class Application
{
  public function start()
  {
    $router = new Router();

    $router->create("GET", "/", function () {
      http_response_code(200);
      return;
    });

    $router->create("GET", "/hello", function () {
      http_response_code(200);
      echo json_encode(["hello" => "world"]);
      return;
    });

    $router->init();
  }
}
