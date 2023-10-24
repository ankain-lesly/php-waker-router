<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter;

use Devlee\WakerRouter\Exceptions\NotFoundException;
use Devlee\WakerRouter\Exceptions\RegularException;
use Devlee\WakerRouter\Services\RouterInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
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
  // private static ?string $views_dir = null;

  private array $routes;

  public static Router $router;

  /**
   * @param string $views_dir: main app directory
   */
  public function __construct(string $views_dir)
  {
    $this->request = new Request();
    $this->view = new View($views_dir);
    $this->response = new Response($this->view);

    // self::$views_dir = $views_dir;
    self::$router = $this;
  }

  /**
   * Set page layout for view content
   * 
   * @method SetLayout
   */
  public static function setLayout(string $layout)
  {
    return View::setLayoutsDir($layout);
  }

  /**
   * Configure router option and views
   * 
   * @param string $main_layout Add a site layout view|template if available >> MainLayout
   * @param string $not_found_view Your not found page >> 404
   */
  public function config(string $main_layout, string $not_found_view = null)
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

      if (!class_exists($class)) {
        throw new RegularException('Object Class Not Found in your Application: ' . "<mark>$class</mark>");
      } elseif (!method_exists($class, $method))
        throw new RegularException("Method: <mark>$method</mark> does not exist in " . "<mark>$class</mark>");

      $class = new $class();
      call_user_func([$class, $method], $this->request, $this->response);
      exit;
    }

    # Callable Handler
    if (is_callable($handler)) {
      call_user_func($handler, $this->request, $this->response);
      exit;
    }

    # FallBack Not FOund page Handler

    if (self::$NOT_FOUND_VIEW) {
      # Rendering a Not Found View
      $this->view->render(self::$NOT_FOUND_VIEW, "Page Not Found");
    }
    # Throwing an Error
    throw new NotFoundException();
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
  }

  /**
   * Set a Template Engine
   */
  public function setTemplateEngine(object $engine)
  {
    $this->view->setEngine($engine);
  }
}
