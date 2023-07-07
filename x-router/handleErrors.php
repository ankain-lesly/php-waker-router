<?php

namespace app\router;

class handleErrors
{
  private const VALIDATION_ERROR = 400;
  private const UNAUTHORIZED = 401;
  private const FORBIDDEN = 403;
  private const NOT_FOUND = 404;
  private const SERVER_ERROR = 500;
  private const ROUTER_DIR_ERROR = 444;

  public static function createExceptionError(\Throwable $e)
  {
    $statusObject = array(
      self::VALIDATION_ERROR => [
        "code" => self::VALIDATION_ERROR,
        "title" => "Validation Failed",
      ],
      self::ROUTER_DIR_ERROR => [
        "code" => self::ROUTER_DIR_ERROR,
        "title" => "Error Getting View File",
      ],
      self::NOT_FOUND => [
        "code" => self::NOT_FOUND,
        "title" => "Not Found",
      ],
      self::FORBIDDEN => [
        "code" => self::FORBIDDEN,
        "title" => "Forbidden",
      ],
      self::SERVER_ERROR => [
        "code" => self::SERVER_ERROR,
        "title" => "Server Error",
      ],
      self::UNAUTHORIZED => [
        "code" => self::UNAUTHORIZED,
        "title" => "Unauthorized",
      ],
    );

    if (array_key_exists($e->getCode(), $statusObject)) {
      $stack = $statusObject[$e->getCode()];
      return self::sendErrorMessage($stack['title'], $e);
    }

    $title = "Unknown Request";
    return self::sendErrorMessage($title, $e);
  }

  private static function sendErrorMessage($title, $e)
  {
    $message = "<b>$title: </b>";
    $message .= $e->getMessage();

    echo ($message);
    exit;
  }
}
