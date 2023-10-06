<?php

/**
 * User: Dev_Lee
 * Date: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter\Services;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\handleErrors
 */


interface RouterInterface
{
  public function get(string $path, $handler): void;
  public function post(string $path, $handler): void;
  public function both(string $path, $handler): void;
  public function delete(string $path, $handler): void;
  public function put(string $path, $handler): void;
  public function patch(string $path, $handler): void;

  public function useRoutes(array $customRoutes): void;

  public function resolve(): void;
}
