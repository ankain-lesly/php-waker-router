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

class RouterException extends \Exception
{
  protected $message;
  protected $code;

  public function __construct(string $message, int $code = 500)
  {
    $this->message = $message;
    $this->code = $code;
    handleErrors::createExceptionError($this);
    exit;
  }
}
