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

class ViewNotFoundException extends RouterBaseException
{
  public function __construct(string $view_type, string $filename,)
  {

    $filename = str_replace('/', '\\', $filename);
    $message = 'Oops! we encountered an error loading your ' . $view_type . ' file. ';
    $message .= "<mark><b> $filename.php </b></mark>, <b>html.</b> Check if it exist in your directory and try again!";

    parent::__construct($message, 404);

    HandleErrors::DisplayErrorMessage($this);
  }
}
