<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */


namespace Devlee\PHPRouter\Exceptions;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-router-core
 */

class HandleErrors
{
  public static function DisplayErrorMessage(RouterBaseException $e)
  {
    http_response_code($e->getCode());

    $message = "<br><b>Title:</b> " . $e->getTitle();
    $message .= "<br><b>Message:</b> " . $e->getMessage();
    $message .= "<br><b>Status code:</b> " . $e->getCode();

    echo ($message);
    exit;
  }
}
