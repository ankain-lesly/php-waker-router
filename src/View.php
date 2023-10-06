<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter;

use Devlee\PHPRouter\Exceptions\ViewNotFoundException;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Devlee\PHPRouter\handleErrors
 */

class View
{
  /**
   * @property
   */
  # configs
  public static ?string $VIEWS_DIR = 'views';
  public static ?string $LAYOUT_DIR = null;
  public static ?string $ROOT_DIR = null;
  public static ?string $NOT_FOUND_VIEW = null;

  # View page title
  private static string $page_title = "Router: Page title";

  /**
   * @property
   */
  public function __construct(string $root_directory)
  {
    self::$ROOT_DIR =  $root_directory;
  }

  /**
   * Display the Content|String|Text|html provided without layout
   * @method Mapping Views
   */
  public function content(string $text_content)
  {
    # Load Data into view
    $view = $text_content;
    $layout = $this->getLayoutContent();
    if ($layout) {
      exit(str_replace("{{content}}", $view, $layout));
    }
    exit($view);
  }
  /**
   * Display the provided view|template with no layout
   * @method Mapping Views
   */
  public function viewOnly(string $view, $object_data = [])
  {
    exit($this->getViewContent($view, $object_data));
  }

  /**
   * Display view|template in layout if available
   * @method Mapping Views
   */
  public function render(string $view, array $object_data = [])
  {
    # Load Data into view
    $view = $this->getViewContent($view, $object_data);

    $layout = $this->getLayoutContent();
    if ($layout) {
      exit(str_replace("{{content}}", $view, $layout));
    }
    exit($view);
  }

  /**
   * @method
   * Load a view content
   */
  private function getViewContent(string $view, $params = [])
  {
    $file = self::$ROOT_DIR . '/../' . $view;


    if (self::$VIEWS_DIR) {
      $file = self::$ROOT_DIR . '/../' . self::$VIEWS_DIR . '/' . $view;
    }

    if (file_exists($file . ".php"))
      return self::loadViewFile($file . ".php", $params);
    elseif (file_exists($file . ".html"))
      return self::loadViewFile($file . ".html", $params);
    elseif (file_exists($file))
      return self::loadViewFile($file, $params);

    throw new ViewNotFoundException("View", $file);
  }

  /**
   * @method
   * Load layout content if available
   */
  private function getLayoutContent()
  {
    $layout_view = self::$LAYOUT_DIR;

    if (!$layout_view) return false;

    $file = self::$ROOT_DIR . '/../' . self::$VIEWS_DIR . '/' . $layout_view;

    if (file_exists($file . ".php"))
      return self::loadViewFile($file . ".php");
    elseif (file_exists($file . ".html"))
      return self::loadViewFile($file . ".html");

    $file = self::$LAYOUT_DIR ? self::$LAYOUT_DIR . '/' : '';

    throw new ViewNotFoundException("Layout", $layout_view);
  }


  /**
   * @method
   * Get a single file from views
   */
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

  /**
   * @property
   * Handling Page Title
   */
  public static function setPageTitle(string $page_title)
  {
    self::$page_title = $page_title;
  }
  public static function getPageTitle(): string
  {
    return self::$page_title;
  }
}
