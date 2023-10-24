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

class ViewNotFoundException extends _RouterBaseException
{
  public function __construct(string $view_type, string $filename)
  {
    $title = 'Error Getting View file';

    if (strtolower($view_type) !== 'view') {
      $title = 'Error Getting Layout file';
    }

    $message = 'Oops! We encountered an error loading your ' . $view_type . ' file. ';
    $message .= " Check if it exist in your directory and try again!";

    parent::__construct($message, $title, 404);

    $filename = str_replace('/', '\\', $filename) . ".php | .html | .twig";
    $filename = explode('\..\\', $filename);

    $context = array(
      'view' => end($filename) . ' not found',
    );
    if (!str_contains($this->getTrace()[2]['file'] ?? 'No file', 'devlee')) {
      $context['file'] = $this->getTrace()[2]['file'] ?? 'No file';
      $context['line'] = $this->getTrace()[2]['line'] ?? 'No Line';
    }

    HandleRouterExceptions::setup($this, $context);
  }
}
