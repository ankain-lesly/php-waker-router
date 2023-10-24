<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/23/2023 - Time: 7:39 PM
 */


namespace Devlee\WakerRouter\Exceptions;

use Devlee\ErrorTree\ErrorTree;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

class HandleRouterExceptions
{
  public static function setup(_RouterBaseException $e, array $context = [])
  {
    $ExcData = array(
      'title' => $e->getTitle(),
      'code' => $e->getCode(),
      'message' => $e->getMessage(),
    );

    if ($context)
      $ExcData = array_merge($ExcData, $context);

    ErrorTree::RenderError($ExcData);
  }
}
