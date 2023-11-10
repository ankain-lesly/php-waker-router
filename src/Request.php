<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

class Request
{
  private array $bodyData = [];
  private array $queryData = [];
  private array $paramsData = [];

  public function __construct()
  {
  }

  // HAndler MEthods
  public function uri()
  {
    return $_SERVER['REQUEST_URI'] ?? '/';
  }
  public function path()
  {
    $path = $_SERVER['REQUEST_URI'] ?? '/';
    $position = strpos($path, '?');

    if ($position) {
      $path = substr($path, 0, $position);
    }

    return $path;
  }

  public function isGet()
  {
    return $this->method() === 'GET';
  }

  public function isPost()
  {
    return $this->method() === 'POST';
  }

  public function method()
  {
    $method = $_SERVER['REQUEST_METHOD'];
    return strtoupper($method);
  }

  // PARAMS DATA
  public function setParams($params = [])
  {
    foreach ($params as $key => $value) {
      $this->paramsData[$key] = $this->sanitizeParams($value);
    }
  }
  public function params(string $key = null)
  {
    if ($key) return  $this->paramsData[$key] ?? false;
    return  $this->paramsData;
  }
  //  BODY DATA
  public function setBody($params = null)
  {
    if ($params) {
      foreach ($params as $key => $value) {
        $this->bodyData[$key] = $this->sanitizeParams($value);
      }
    }
  }
  // Get GET or Request PARAMS
  public function query(string $param_key = null)
  {
    foreach ($_GET as $key => $value) {
      $this->queryData[$key] = $this->sanitizeParams($value);
    }

    if ($param_key) return $this->queryData[$param_key] ?? false;

    return $this->queryData;
  }
  // Get POST or Request BODY
  public function body(string $param_key = null)
  {
    // Converts Raw Data into a PHP object
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data) {
      foreach ($data as $key => $value) {
        $this->bodyData[$key] = $this->sanitizeParams($value);
      }
    }

    if ($this->isPost()) {
      foreach ($_POST as $key => $value) {
        $this->bodyData[$key] = $this->sanitizeParams($value);
      }
    }

    if ($param_key) return $this->bodyData[$param_key] ?? false;

    return $this->bodyData;
  }

  // TODO:
  private function sanitizeParams($value)
  {
    // return filter_input(INPUT_GET, $param, FILTER_SANITIZE_SPECIAL_CHARS);
    // $value = htmlspecialchars($value);
    // $value = strip_tags($value);
    // $value = trim($value);
    if (is_string($value)) {
      return $value;
    }
    return $value;
  }

  /**
   * Filter a given string and remove HTML Exec Characters
   * @method filterString
   * 
   * @param string $value
   */
  public function filterString($value)
  {
    if (is_string($value)) {
      $value = htmlspecialchars($value);
      $value = strip_tags($value);
      $value = trim($value);
      return $value;
    }

    return $value;
  }
}
