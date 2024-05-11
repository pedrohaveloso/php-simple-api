<?php

namespace SimpleApi\Core;

class Response
{

  public function __construct(
    public int $statusCode,
    public string|array $body = "",
  ) {
    if (is_array($body)) {
      $this->body = json_encode($this->body, true);
    }
  }
}
