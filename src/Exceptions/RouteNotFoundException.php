<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter\Exceptions;

use Devlee\PHPRouter\Router;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-router-core
 */

class RouteNotFoundException extends RouterBaseException
{
  public function __construct(private ?string $view = null)
  {

    parent::__construct('This page is not available', 404);

    if ($view) {
      Router::$router->getResponse()->render($view);
    }
    HandleErrors::DisplayErrorMessage($this);
  }
}
