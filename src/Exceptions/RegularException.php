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

class RegularException extends _RouterBaseException
{
  public function __construct(string $message, ?string $title = null)
  {
    if (!$title) {
      $title = "Router Caught an Error";
    }

    parent::__construct($message . '; Check your Routes file and try again', $title, 404);

    HandleRouterExceptions::setup($this);
  }
}
