<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter;

use Devlee\WakerRouter\Services\TemplatesTrait;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

class Response
{
  // Templates Routine
  use TemplatesTrait;

  /**
   * @param View View Handler
   */
  public function __construct(private View $viewHandler)
  {
  }
  public function status(int $code)
  {
    http_response_code($code);
    return $this;
  }
  public function refresh(int $time_seconds = 0, string $url = null)
  {
    header("Refresh: $time_seconds; url=$url");
    exit;
  }
  public function redirect(string $url, int $code = 301)
  {
    $this->status($code);
    header("Location: $url");
    exit;
  }

  public function json(array $data)
  {
    exit(json_encode($data));
  }
}
