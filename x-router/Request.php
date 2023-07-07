<?php

/** User: Dev Lee ... */

namespace app\router;

/**
 * Class Router
 * 
 * @author Ankain Lesly <leeleslyank@gmail.com>
 * @package app\core
 */

class Request
{
  private array $body = [];
  private array $query_params = [];
  public string $ROOT_DIR;

  public function __construct(string $root_dir)
  {
    $this->ROOT_DIR = $root_dir;
  }
  public function path()
  {
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');

    if ($position) {
      $path = substr($path, 0, $position);
    }

    $dir = explode('\\', $this->ROOT_DIR);
    $path = explode('/', $path);

    $app_path = [];
    foreach ($path as $key) {
      if (!in_array($key, $dir)) {
        $app_path[] = $key;
      }
    }
    return implode('/', $app_path);
  }

  public function isGet()
  {
    return $this->method() === 'get';
  }

  public function isPost()
  {
    return $this->method() === 'post';
  }

  public function method()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    return strtolower($method);
  }

  // PARAMS DATA
  public function setParams($params = [])
  {
    foreach ($params as $key => $value) {
      $this->query_params[$key] = $this->sanitizeParams($value);
    }
  }
  public function params(string $key = null)
  {
    if ($key) return  $this->query_params[$key] ?? false;
    return  $this->query_params;
  }
  //  BODY DATA
  public function setBody($params = null)
  {
    if ($params) {
      foreach ($params as $key => $value) {
        $this->body[$key] = $this->sanitizeParams($value);
      }
    }
  }
  public function body(string $param_key = null)
  {
    // Converts Raw Data into a PHP object
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
      foreach ($data as $key => $value) {
        $this->body[$key] = $this->sanitizeParams($value);
      }
    }

    if ($this->isGet()) {
      foreach ($_GET as $key => $value) {
        $this->body[$key] = $this->sanitizeParams($value);
      }
    }

    if ($this->isPost()) {
      foreach ($_POST as $key => $value) {
        $this->body[$key] = $this->sanitizeParams($value);
      }
    }

    if ($param_key) return $this->body[$param_key] ?? false;

    return $this->body;
  }

  private function sanitizeParams(string $value)
  {
    // return filter_input(INPUT_GET, $param, FILTER_SANITIZE_SPECIAL_CHARS);
    $value = htmlspecialchars($value);
    $value = strip_tags($value);
    $value = trim($value);
    return $value;
  }

  // private function headers(string $value) {

  // }

}
