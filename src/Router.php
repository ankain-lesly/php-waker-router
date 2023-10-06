<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
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
   * Unimplemented requests methods
   * 
   * @property
   * 
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


  private array $routes;

  private static ?string $ROOT_DIR = null;

  public static Router $router;

  public function __construct($root_directory)
  {
    $this->request = new Request($root_directory);
    $this->response = new Response($root_directory);
    self::$ROOT_DIR = $root_directory;
    self::$router = $this;
  }


  /**
   * Set page layout for view content
   * 
   * @method SetLayout
   */
  public static function setLayout(string $layout_dir)
  {
    return self::$router->response::$LAYOUT_MAIN = $layout_dir;
  }
  public function config(string $views_folder, string $main_layout, string $not_found_page)
  {
    $this->response::$VIEWS_MAIN = $views_folder;
    $this->response::$LAYOUT_MAIN = $main_layout;
    View::$NOT_FOUND = $not_found_page;
  }

  /**
   * @param string|array|callable $handler
   */

  private function registerRoute(string $method, string $path, $handler): void
  {
    /**
     * Searching for URI parameter >>> {param_name} in $path
     * 
     */

    preg_match_all("/(?<={).+?(?=})/", $path, $paramMatchesKeys);

    if (empty($paramMatchesKeys[0])) {
      $this->routes[$method][$path] = $handler;
    } else {
      $path = $this->routeParamsFactory($path, $paramMatchesKeys[0]);

      if ($path) {
        $this->routes[$method][$path] = $handler;
      }
    }
  }

  public function get(string $path, $handler): void
  {
    $this->registerRoute(self::METHOD_GET, $path, $handler);
  }

  public function post(string $path, $handler): void
  {
  }
  public function both(string $path, $handler): void
  {
  }
  public function delete(string $path, $handler): void
  {
  }
  public function put(string $path, $handler): void
  {
  }
  public function patch(string $path, $handler): void
  {
  }

  public function resolve(): void
  {
    echo '<pre>';
    print_r($this->routes);
    echo '</br>';
    echo '</pre>';
    exit();
    $path = $this->request->path();
    $method = $this->request->method();
    $handler = $this->routes[$method][$path] ?? false;

    # Undefined Page Handler
    if ($handler === false) {
      throw new RouteNotFoundException(View::$NOT_FOUND);
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
          call_user_func($handler, $this->request, $this->response);
        }
      }
    }

    # Callable Handler
    if (is_callable($handler)) {
      call_user_func([$handler], $this->request, $this->response);
    }
    throw new RouteNotFoundException(View::$NOT_FOUND);
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
