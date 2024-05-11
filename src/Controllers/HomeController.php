<?php

namespace SimpleApi\Controllers;

use SimpleApi\Core\Request;
use SimpleApi\Core\Response;

class HomeController
{
  public static function index(Request $request)
  {
    return new Response(200, ["message" => "Hello, {$request->body["name"]}"]);
  }
}
