<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 */

namespace Devlee\PHPRouter;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\handleErrors
 */

class Router
{
  // public static string $ROOT_DIR;
  private array $routes = [];
  private Request $request;
  private Response $response;
  private static ?string $ROOT_DIR = '';
  public static Router $router;

  public static ?string $NOT_FOUND = null;

  public function __construct($root_directory)
  {
    // self::$ROOT_DIR = $root_directory;
    $this->request = new Request($root_directory);
    $this->response = new Response($root_directory);
    self::$ROOT_DIR = $root_directory;
    self::$router = $this;
  }

  // Router config setup
  public static function setLayout(string $layout_dir)
  {
    return self::$router->response::$LAYOUT_MAIN = $layout_dir;
  }
  public function config(string $views_folder, string $main_layout, string $not_found_page)
  {
    $this->response::$VIEWS_MAIN = $views_folder;
    $this->response::$LAYOUT_MAIN = $main_layout;
    self::$NOT_FOUND = $not_found_page;
  }

  public function get($path, $handler)
  {
    //finding if there is any {?} parameter in $path
    preg_match_all("/(?<={).+?(?=})/", $path, $paramMatchesKeys);

    if (empty($paramMatchesKeys[0])) {
      return $this->routes['get'][$path] = $handler;
    }

    $response = $this->getQueryParams($path, $paramMatchesKeys[0]);

    if ($response) {
      $this->routes['get'][$response] = $handler;
    }
  }

  public function post($path, $handler)
  {
    //finding if there is any {?} parameter in $path
    preg_match_all("/(?<={).+?(?=})/", $path, $paramMatchesKeys);

    if (empty($paramMatchesKeys[0])) {
      return $this->routes['post'][$path] = $handler;
    }

    $response = $this->getQueryParams($path, $paramMatchesKeys[0]);

    if ($response) {
      $this->routes['post'][$response] = $handler;
    }
  }
  public function both($path, $handler)
  {
    //finding if there is any {?} parameter in $path
    preg_match_all("/(?<={).+?(?=})/", $path, $paramMatchesKeys);

    if (empty($paramMatchesKeys[0])) {
      $this->routes['get'][$path] = $handler;
      return $this->routes['post'][$path] = $handler;
    }

    $response = $this->getQueryParams($path, $paramMatchesKeys[0]);

    if ($response) {
      $this->routes['get'][$response] = $handler;
      $this->routes['post'][$response] = $handler;
    }
  }

  private function getQueryParams(
    $path,
    $paramKey
  ) {
    $uri = $this->request->path();
    $params = [];

    //exploding path and request uri string to array
    $path = explode("/", $path);
    $reqUri = explode("/", $uri);

    if (count($path) !== count($reqUri)) return false;

    //will store index number where {?} parameter is required in the $path 
    $indexNum = [];

    //storing index number, where {?} parameter is required with the help of regex
    foreach ($path as $index => $param) {

      if (preg_match("/{.*}/", $param)) {
        $indexNum[] = $index;
        continue;
      }

      if ($path[$index] !== $reqUri[$index]) return false;
    }

    //running for each loop to set the exact index number with reg expression
    foreach ($indexNum as $key => $index) {
      if (empty($reqUri[$index])) {
        return;
      }
      //setting params with params names
      $params[$paramKey[$key]] = $reqUri[$index];
      $path[$index] = $reqUri[$index];
      // $reqUri[$index] = "{.*}";
    }

    $this->request->setParams($params);
    return implode("/", $path);
  }

  public function resolve()
  {
    $response = $this->response;
    $path = $this->request->path();
    $method = $this->request->method();
    $handler = $this->routes[$method][$path] ?? false;

    # Undefined Page Handler
    if ($handler === false) {

      if (self::$NOT_FOUND) {
        $response
          ->status(404)
          ->render(Router::$router::$NOT_FOUND);
      }
      throw new RouterException('Oops! The page you are trying to access is not available.', 404);
    }

    # String Handler
    if (is_string($handler)) {

      if (str_contains($handler, '@')) {
        $handler = str_replace('@', '', $handler);
        return $response->content($handler);
      }
      return $response->render($handler);
    }

    # Array Handler
    if (is_array($handler)) {
      $handler[0] = new $handler[0]();
    }
    # Reset Routes Object
    $this->routes = [];
    call_user_func($handler, $this->request, $response);
  }

  public function interceptRequest($path = null)
  {
    $server_root = str_replace('\\', "/", strtolower($_SERVER['DOCUMENT_ROOT']));
    $app_root = str_replace('\\', "/", strtolower(self::$ROOT_DIR));

    $route = str_replace($server_root, '', $app_root);

    $path = $path ?? $this->request->path();

    if (!str_contains($path, '_/')) return;
    $path = explode('_/', $path);
    $path = '/' . end($path);

    $route = $route . $path;
    $this->response->redirect($route, 200);
  }

  // Get Response
  public function getResponse()
  {
    return $this->response;
  }

  // FILE PATH
  public static function root_folder()
  {
    return self::$ROOT_DIR;
  }
}
