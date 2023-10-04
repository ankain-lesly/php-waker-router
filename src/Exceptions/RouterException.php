<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 */


namespace Devlee\PHPRouter\Exceptions;

interface CustomException
{
  public function getTitle();
}

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\Exceptions\RouterException
 */

class RouterException extends \Exception implements CustomException
{
  private const VALIDATION_ERROR = 400;
  private const UNAUTHORIZED = 401;
  private const FORBIDDEN = 403;
  private const NOT_FOUND = 404;
  private const SERVER_ERROR = 500;
  private const ROUTER_DIR_ERROR = 444;

  protected $message;
  protected $code;
  protected $title;

  public function __construct(string $message, int $code = 500)
  {
    $this->message = $message;
    $this->code = $code;
    $this->title = self::getErrorTitle($code);
    HandleErrors::DisplayErrorMessage($this);
  }

  public function getTitle()
  {
    return $this->title;
  }
  public static function getErrorTitle($code)
  {
    $statusObjectErrors = array(
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
        // "title" => "Resource Not Found",
        "title" => "Error Getting View File",
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

    return $statusObjectErrors[$code]['title'] ?? "Unknown Request";
  }
}
