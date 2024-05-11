<?php

namespace SimpleApi\Core;

abstract class BaseApplication
{
  public function start()
  {
    $this->routes(new Router());
  }

  protected function routes(Router $router)
  {
    $router->init();
  }
}
