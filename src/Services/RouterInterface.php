<?php

namespace Devlee\PHPRouter\Services;

interface RouterInterface
{
  public function get(string $path, $handler): void;
  public function post(string $path, $handler): void;
  public function both(string $path, $handler): void;
  public function delete(string $path, $handler): void;
  public function put(string $path, $handler): void;
  public function patch(string $path, $handler): void;

  public function resolve(): void;
}
