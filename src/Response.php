<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\handleErrors
 */

class Response
{
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
  public function redirect(string $url, ?int $status_code = null)
  {
    $code = $status_code ?? 301;
    $this->status($code);
    header("Location: $url");
    exit;
  }

  public function json(array $data)
  {
    exit(json_encode($data));
  }

  /**
   * Display the Content|String|Text|html provided without layout
   * @method Mapping Views
   */
  public function content(string $text_content)
  {
    $this->viewHandler->content($text_content);
  }
  /**
   * Display the provided view|template with no layout
   * @method Mapping Views
   */
  public function viewOnly(string $view, array $object_data = [])
  {
    $this->viewHandler->viewOnly($view, $object_data);
  }
  /**
   * Display view|template in layout if available
   * @method Mapping Views
   */
  public function render(string $view, array $object_data = [])
  {
    $this->viewHandler->render($view, $object_data);
  }
}
