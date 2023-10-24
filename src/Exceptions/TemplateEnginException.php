<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter\Exceptions;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

class TemplateEnginException extends _RouterBaseException
{
  public function __construct(string $message)
  {
    parent::__construct($message, "Template Engine Error", 500);

    HandleRouterExceptions::setup($this);
  }
}
