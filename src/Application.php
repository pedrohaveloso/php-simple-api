<?php

namespace SimpleApi;

use SimpleApi\Controllers\HomeController;
use SimpleApi\Core\BaseApplication;
use SimpleApi\Core\Router;

class Application extends BaseApplication
{
  protected function routes(Router $router)
  {
    $router->post("/", [HomeController::class, "index"]);

    $router->init();
  }
}
