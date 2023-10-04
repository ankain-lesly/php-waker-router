<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 */

namespace Devlee\PHPRouter;

use Devlee\PHPRouter\Exceptions\RouterException;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\handleErrors
 */

class Response
{
  # configs
  public static ?string $VIEWS_MAIN = null;
  public static ?string $LAYOUT_MAIN = null;

  public static string $ROOT_DIR;
  public static string $views_folder = 'views';

  # View page title
  private string $page_title = "Router: Page title";

  public function __construct($root_directory)
  {
    self::$ROOT_DIR =  $root_directory;
  }
  # Setting Page Title
  public function setPageTitle(string $page_title)
  {
    $this->page_title = $page_title;
  }
  public function getPageTitle(): string
  {
    return $this->page_title;
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

  public function content(string $text_content)
  {
    # Load Data into view
    $view = $text_content;
    $layout = $this->getLayout();
    if ($layout) {
      exit(str_replace("{{content}}", $view, $layout));
    }

    exit($view);
  }
  public function sendPage(string $pageView, $params = [])
  {
    exit($this->getView($pageView, $params));
  }
  public function render(string $view, array $params = [])
  {
    # Load Data into view
    $view = $this->getView($view, $params);
    $layout = $this->getLayout();
    if ($layout) {
      exit(str_replace("{{content}}", $view, $layout));
    }

    exit($view);
  }
  public function json(array $data)
  {
    exit(json_encode($data));
  }

  private function getView(string $filename, $params = [])
  {
    $file = self::$ROOT_DIR . '/' . $filename;

    if (self::$VIEWS_MAIN) {
      $file = self::$ROOT_DIR . '/' . self::$VIEWS_MAIN . '/' . $filename;
    }

    if (file_exists($file . ".php"))
      return self::loadViewFile($file . ".php", $params);
    elseif (file_exists($file . ".html"))
      return self::loadViewFile($file . ".html", $params);
    elseif (file_exists($file))
      return self::loadViewFile($file, $params);

    $file = self::$VIEWS_MAIN ? self::$VIEWS_MAIN . '/' : '';
    $message = 'Ooops! Your file could not be located. <br />Check the filename and try again ';
    $message .= "<mark><b> " . $file  . $filename . ".php </b></mark>, <b>html.</b> Check the if it exist in your directory and try again!";
    throw new RouterException($message, 404);
  }
  private function getLayout()
  {
    $layout_view = self::$LAYOUT_MAIN;
    if (!$layout_view) return false;

    $file = self::$ROOT_DIR . '/' . self::$VIEWS_MAIN . '/' . $layout_view;

    if (file_exists($file . ".php"))
      return self::loadViewFile($file . ".php");
    elseif (file_exists($file . ".html"))
      return self::loadViewFile($file . ".html");

    $file = self::$LAYOUT_MAIN ? self::$LAYOUT_MAIN . '/' : '';
    $message = '<u>Getting Layout Failed.</u> Ooops! Your file could not be located. Check the filename and try again ';
    $message .= "<mark><b> " . $layout_view . ".php </b></mark>, <b>html.</b> Check the if it exist in your directory and try again!";

    throw new RouterException($message, 404);
  }

  # Load file
  private static function loadViewFile(string $view_file, array $params = [])
  {
    # Load Data into view
    if ($params) {
      foreach ($params as $key => $value) {
        $$key  = $value;
      }
    }

    ob_start();
    include_once $view_file;
    return ob_get_clean();
  }
}
