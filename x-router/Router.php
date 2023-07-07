<?php

/** User: Dev Lee ... */

namespace app\router;

use app\router\RouterException;

/**
 * Class Router
 * 
 * @author Ankain Lesly <leeleslyank@gmail.com>
 * @package app\router
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
  public function setLayout(string $layout_dir)
  {
    return $this->response::$LAYOUT_MAIN = $layout_dir;
  }
  public function config(string $views_folder, string $main_layout, string $not_found_page)
  {
    $this->response::$VIEWS_MAIN = $views_folder;
    $this->response::$LAYOUT_MAIN = $main_layout;
    self::$NOT_FOUND = $not_found_page;
  }

  public function get($path, $callback)
  {
    //finding if there is any {?} parameter in $path
    preg_match_all("/(?<={).+?(?=})/", $path, $paramMatchesKeys);

    if (empty($paramMatchesKeys[0])) {
      return $this->routes['get'][$path] = $callback;
    }

    $response = $this->getQueryParams($path, $paramMatchesKeys[0]);

    if ($response) {
      $this->routes['get'][$response] = $callback;
      // $this->request->setBody($response['params']);
    }
  }

  public function post($path, $callback)
  {
    $this->routes['post'][$path] = $callback;
  }

  private function getQueryParams($path, $paramKey)
  {
    $uri = $this->request->path();
    $params = [];

    //exploding path and request uri string to array
    $path = explode("/", $path);
    $reqUri = explode("/", $uri);

    //will store index number where {?} parameter is required in the $path 
    $indexNum = [];

    //storing index number, where {?} parameter is required with the help of regex
    foreach ($path as $index => $param) {

      if (preg_match("/{.*}/", $param)) {
        $indexNum[] = $index;
        continue;
      }

      if ($path[$index] !== $reqUri[$index]) return implode('/', $path);
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
    $callback = $this->routes[$method][$path] ?? false;
    // try {
    //Undefined Page Handler

    if ($callback === false) {
      if ($path !== "/404") return $this->interceptRequest("_/404?resource=$path");

      if (self::$NOT_FOUND) {
        $response
          ->status(404)
          ->render(Router::$router::$NOT_FOUND);
      }
      throw new RouterException('Oops! The page you are trying to access is not available.', 404);
    }

    //String Handler
    if (is_string($callback)) {

      if (str_contains($callback, '@')) {
        $callback = str_replace('@', '', $callback);
        return $response->render($callback);
      }
      return $response->content($callback);
    }

    //Array Handler
    if (is_array($callback)) {
      $callback[0] = new $callback[0]();
      // Application::$app->setController($callback[0]);
    }

    call_user_func($callback, $this->request, $response);
  }

  public function interceptRequest($path = null)
  {
    $server_root = $_SERVER['DOCUMENT_ROOT'];
    $uri = $path ?? $this->request->path();

    if (!str_contains($uri, '_/')) return;

    $uri = explode('_/', $uri);
    $uri = '/' . end($uri);

    $app_root = str_replace('\\', "/", self::$ROOT_DIR);
    $server_root = str_replace('\\', "/", $server_root);
    $root = str_replace($server_root, '', $app_root);

    $this->response->redirect($root . $uri, 200);
  }

  // Get Response
  public function getResponse()
  {
    return $this->response;
  }
}
