<?php

namespace app\router;

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
