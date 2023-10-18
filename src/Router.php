<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter;

use Devlee\PHPRouter\Exceptions\RouteNotFoundException;
use Devlee\PHPRouter\Services\RouterInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-router-core
 */

class Router implements RouterInterface
{
  /**
   * General request methods
   * 
   */

  private const METHOD_GET = 'GET';
  private const METHOD_POST = 'POST';
  private const METHOD_PUT = 'PUT';
  private const METHOD_PATCH = 'PATCH';
  private const METHOD_DELETE = 'DELETE';

  private const METHOD_OPTIONS = 'OPTIONS';

  private const METHOD_PURGE = 'PURGE';
  private const METHOD_HEAD = 'HEAD';
  private const METHOD_TRACE = 'TRACE';
  private const METHOD_CONNECT = 'CONNECT';


  /**
   * @property
   */
  protected Response $response;
  protected Request $request;
  protected View $view;

  private static ?string $NOT_FOUND_VIEW = null;
  private static ?string $ROOT_PATH = null;

  private array $routes;

  public static Router $router;

  /**
   * @param string $root_path: main app directory
   */
  public function __construct($root_path)
  {
    $this->request = new Request($root_path);
    $this->view = new View($root_path);
    $this->response = new Response($this->view);

    self::$ROOT_PATH = $root_path;
    self::$router = $this;
  }

  /**
   * Set page layout for view content
   * 
   * @method SetLayout
   */
  public static function setLayout(string $layout_directory)
  {
    return View::setLayoutsDir($layout_directory);
  }

  /**
   * Configure router option
   * @param $views_dir main name folder where views|templates are located
   * @param $main_layout main layout of the app if available
   * @param $not_found_view Your not found page
   * 
   * @method config
   * @return void
   */
  public function config(string $main_layout, string $not_found_view)
  {
    $this->view::setLayoutsDir($main_layout);
    self::$NOT_FOUND_VIEW = $not_found_view;
  }

  /**
   * @param string|array|callable $handler
   */

  private function registerRoute(string $method, string $path, $handler): void
  {
    /**
     * Searching for URI parameter >>> {param_name} in $path
     * @var $paramMatchKeys
     * 
     */

    preg_match_all("/(?<={).+?(?=})/", $path, $match);

    if (empty($match[0])) {
      $this->routes[$method][$path] = $handler;
    } else {
      $path = $this->routeParamsFactory($path, $match[0]);

      if ($path) {
        $this->routes[$method][$path] = $handler;
      }
    }
  }

  /**
   * @method get
   */
  public function get(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_GET, $path, $handler);
  }

  /**
   * @method post
   */
  public function post(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_POST, $path, $handler);
  }
  /**
   * @method both
   */
  public function both(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_POST, $path, $handler);
    $this->registerRoute(self::METHOD_GET, $path, $handler);
  }
  /**
   * @method delete
   */
  public function delete(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_DELETE, $path, $handler);
  }
  /**
   * @method put
   */
  public function put(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_PUT, $path, $handler);
  }
  /**
   * @method patch
   */
  public function patch(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_PATCH, $path, $handler);
  }

  /**
   * Register a collection of routes
   * @param array<callable, mixed> customRoutes
   * @method addRoutes
   */
  public function addRoutes(array $customRoutes): void
  {
    foreach ($customRoutes as $route) {
      if (is_callable($route)) {
        $route($this);
      }
    }
  }

  /**
   * Register a route
   * @param array<callable, mixed> customRoutes
   * @method useRoute
   */
  public static function useRoute(): self
  {
    return self::$router;
  }
  /**
   * Execute  Routes
   * @method resolve
   */
  public function resolve(): void
  {
    $path = $this->request->path();
    $method = $this->request->method();
    $handler = $this->routes[$method][$path] ?? false;

    # Undefined Page Handler
    if ($handler === false) {
      throw new RouteNotFoundException(self::$NOT_FOUND_VIEW);
    }

    # String Handler
    if (is_string($handler)) {

      if (str_contains($handler, '@')) {
        $view = str_replace('@', '', $handler);
        $this->response->render($view);
      }

      $this->response->content($handler);
    }

    # Array Handler 
    if (is_array($handler)) {
      [$class, $method] = $handler;

      $class = new $class();
      # Reset Routes Object
      call_user_func([$class, $method], $this->request, $this->response);
      exit;
    }

    # Callable Handler
    if (is_callable($handler)) {
      call_user_func($handler, $this->request, $this->response);
      exit;
    }
    throw new RouteNotFoundException(self::$NOT_FOUND_VIEW);
  }

  /**
   * Simply Generate Query Prams
   * 
   * @method RouteParamsFactory
   */
  protected function routeParamsFactory($path, $paramKey)
  {
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
  /**
   * This method can only be used when the application does not run on a server
   * For example running under a sub directory in a host
   * 
   * @method interceptRequest
   */
  public function interceptRequest(?string $path = null): void
  {
    $server_root = str_replace('\\', "/", strtolower($_SERVER['DOCUMENT_ROOT']));
    $app_root = str_replace('\\', "/", strtolower(self::$ROOT_PATH));

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

  public static function __callStatic($name, $args)
  {
    if (method_exists(self::$router, $name)) {
      self::$router->$name(...$args);
    } else {
      $message = "<br /> <b>Router Error</b>";
      $message .= "<br /> Unknown Router modifier <b>$name</b>";
      die($message);
    }
    /**
     * AddRoute     >>> Add Static Route
     * GroupRoutes  >>> Group Routes statically
     */
  }
}
