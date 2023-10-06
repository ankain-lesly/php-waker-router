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
 * @package  Devlee\PHPRouter\handleErrors
 */

class Router implements RouterInterface
{
  /**
   * General request methods
   * 
   * @property
   * 
   */

  public const METHOD_GET = 'GET';
  public const METHOD_POST = 'POST';
  public const METHOD_PUT = 'PUT';
  public const METHOD_PATCH = 'PATCH';
  public const METHOD_DELETE = 'DELETE';
  public const METHOD_OPTIONS = 'OPTIONS';
  /**
   * @property
   */
  public const METHOD_PURGE = 'PURGE';
  public const METHOD_HEAD = 'HEAD';
  public const METHOD_TRACE = 'TRACE';
  public const METHOD_CONNECT = 'CONNECT';


  /**
   * @property
   * 
   */
  protected Response $response;
  protected Request $request;
  protected View $view;


  private array $routes;

  private static ?string $ROOT_DIR = null;

  public static Router $router;

  /**
   * @param string $root_directory: main app directory
   */
  public function __construct($root_directory)
  {
    $this->request = new Request($root_directory);
    $this->view = new View($root_directory);
    $this->response = new Response($this->view);

    self::$ROOT_DIR = $root_directory;
    self::$router = $this;
  }

  /**
   * Set page layout for view content
   * 
   * @method SetLayout
   */
  public static function setLayout(string $layout_directory)
  {
    return self::$router->view::$LAYOUT_DIR = $layout_directory;
  }
  public function config(string $views_folder, string $main_layout, string $not_found_view)
  {
    $this->view::$VIEWS_DIR = $views_folder;
    $this->view::$LAYOUT_DIR = $main_layout;
    $this->view::$NOT_FOUND_VIEW = $not_found_view;
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
   * Register Custom router
   * @param callable[]  customRoutes
   */
  public function useRoutes(array $customRoutes): void
  {
    foreach ($customRoutes as $route) {
      if (is_callable($route)) {
        $route($this);
      }
    }
  }
  /**
   * @method Resolve Routes
   */
  public function resolve(): void
  {
    $path = $this->request->path();
    $method = $this->request->method();
    $handler = $this->routes[$method][$path] ?? false;

    echo '</br>';
    // echo '</pre>';

    # Undefined Page Handler
    if ($handler === false) {
      throw new RouteNotFoundException(View::$NOT_FOUND_VIEW);
    }

    # String Handler
    if (is_string($handler)) {

      if (str_contains($handler, '@')) {
        $handler = str_replace('@', '', $handler);
        $this->response->content($handler);
      }

      $this->response->render($handler);
    }

    # Array Handler
    if (is_array($handler)) {
      [$class, $method] = $handler;

      if (class_exists($class)) {
        $class = new $class();
        if (method_exists($class, $method)) {
          # Reset Routes Object
          call_user_func([$class, $method], $this->request, $this->response);
          exit;
        }
      }
    }

    # Callable Handler
    if (is_callable($handler)) {
      call_user_func($handler, $this->request, $this->response);
      exit;
    }
    throw new RouteNotFoundException(View::$NOT_FOUND_VIEW);
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
}
