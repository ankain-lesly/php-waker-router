<?php

/**
 * User: Dev_Lee
 * Date: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter\Services;

use Devlee\WakerRouter\Router;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */


interface RouterInterface
{
  public function get(string $path, $handler): void;
  public function post(string $path, $handler): void;
  public function both(string $path, $handler): void;
  public function delete(string $path, $handler): void;
  public function put(string $path, $handler): void;
  public function patch(string $path, $handler): void;

  public function addRoutes(array $customRoutes): void;
  public static function useRoute(): Router;

  public function resolve(): void;
}
