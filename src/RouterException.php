<?php

/**
 * User: Dev_Lee
 * Date: 6/29/2023
 * Time: 6:00 AM
 */

namespace Devlee\XRouter;

/**
 * Class BaseModel
 *
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\mvccore
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
