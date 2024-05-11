<?php

namespace SimpleApi\Core;

class Request
{
  public function __construct(
    public readonly string $method,
    public readonly string $path,
    public readonly array $body,
    public readonly array $query
  ) {
  }
}
