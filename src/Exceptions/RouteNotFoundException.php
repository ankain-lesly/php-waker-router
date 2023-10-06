<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 */


namespace Devlee\PHPRouter\Exceptions;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\Exceptions\RouterException
 */

class RouteNotFoundException extends BaseException
{
  public function __construct(private ?string $view = null)
  {

    parent::__construct('This page is not available', 404);

    echo '<pre>';
    print_r($this);
    echo '</br>';
    echo '</pre>';
    exit();
    HandleErrors::DisplayErrorMessage($this);
  }
}
