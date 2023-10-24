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

abstract class _RouterBaseException extends \Exception
{
  protected string $title = "";

  public function __construct(string $message, string $title, int $code = 0)
  {
    $this->message = $message;
    $this->title = $title;
    $this->code = $code;
  }

  public function getTitle()
  {
    return $this->title;
  }
}
