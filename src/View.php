<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter;

use Devlee\PHPRouter\Exceptions\ViewNotFoundException;
use Wakeable\App\Runner;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-router-core
 */

class View
{
  /**
   *  configs
   */
  private static string $VIEWS_PATH;
  private static ?string $LAYOUT_DIR = null;
  // private static ?string $ROOT_PATH = null;

  # page title
  private static string $page_title = "Router: Page title";

  public function __construct(string $root_path)
  {
    // self::$ROOT_PATH =  $root_path;
    self::$VIEWS_PATH = VIEWS_PATH;
  }

  /**
   * Display the Content|String|Text|html provided without layout
   * @method Mapping Views
   */

  // public static function getViewsDir()
  // {
  //   return self::$VIEWS_DIR;
  // }
  // public static function setViewsDir(string $views_dir)
  // {
  //   self::$VIEWS_DIR = $views_dir;
  // }

  public static function getLayoutsDir()
  {
    return self::$LAYOUT_DIR;
  }
  public static function setLayoutsDir(string $layout_dir)
  {
    self::$LAYOUT_DIR = $layout_dir;
  }

  // public static function getRootDir()
  // {
  //   return self::$ROOT_PATH;
  // }
  // public static function setRootDir(string $root_path)
  // {
  //   self::$ROOT_PATH = $root_path;
  // }

  /**
   * Display the Content|String|Text|html provided without layout
   * @method content
   * @param string $text Text to render on page
   * @param string $title Page title
   * @return null
   */
  public function content(string $text_content, ?string $title = null)
  {
    if ($title) {
      $this->setPageTitle($title);
    }
    $view = $text_content;
    $layout = $this->getLayoutContent();
    if ($layout) {
      $page = str_replace("{{content}}", $view, $layout);
      exit($page);
    }
    exit($view);
  }

  /**
   * Load a view template
   * @method load
   * @param string $view Template view to render
   * @param string $title Page title
   * @param array $context Data to be parsed to view
   * @return null
   */
  public function load(string $view, $context = [], ?string $title = null)
  {
    if ($title) {
      $this->setPageTitle($title);
    }
    $view = $this->getViewContent($view, $context);
    exit($view);
  }

  /**
   * Load a view|template within a main layout if available
   * @method render
   * @param string $view Template view to render
   * @param string $title Page title
   * @param array $context Data to be parsed to view
   * @return null
   */
  public function render(string $view, array $context = [], ?string $title = null)
  {
    if ($title) {
      $this->setPageTitle($title);
    }
    # Load Data into view
    $view = $this->getViewContent($view, $context);

    $layout = $this->getLayoutContent();
    if ($layout) {
      $page = str_replace("{{content}}", $view, $layout);
      exit($page);
    }
    exit($view);
  }

  /**
   * @method
   * Load a view content
   */
  private function getViewContent(string $view, $params = [])
  {
    $file = self::$VIEWS_PATH . '/' . $view;

    // if (self::$VIEWS_DIR) {
    //   $file = self::$ROOT_PATH . '/../' . self::$VIEWS_DIR . '/' . $view;
    // }

    # > Generating Twig Template View Files
    if (file_exists($file . ".twig"))
      return self::LoadTwigTemplate($view . '.twig', $params);

    if (file_exists($file . ".php"))
      return self::loadViewTemplate($file . ".php", $params);
    elseif (file_exists($file . ".html"))
      return self::loadViewTemplate($file . ".html", $params);
    elseif (file_exists($file))
      return self::loadViewTemplate($file, $params);

    throw new ViewNotFoundException("View", $file);
  }

  /**
   * @method getLayoutContent
   * Load layout content
   * @return null
   */
  private function getLayoutContent()
  {
    $layout_view = self::$LAYOUT_DIR;

    if (!$layout_view) return false;

    $file = self::$VIEWS_PATH . '/' . $layout_view;

    if (file_exists($file . ".php"))
      return self::loadViewTemplate($file . ".php");
    elseif (file_exists($file . ".html"))
      return self::loadViewTemplate($file . ".html");

    $file = self::$LAYOUT_DIR ? self::$LAYOUT_DIR . '/' : '';

    throw new ViewNotFoundException("Layout", $file);
  }

  /**
   * @method LoadTwigTemplate
   * Load a twig template
   */
  private static function LoadTwigTemplate(string $view_file, array $params = [])
  {
    return Runner::$app->twig->render($view_file, $params);
  }

  /**
   * Load a view|template from views
   * @method loadViewTemplate
   * @return string
   */
  private static function loadViewTemplate(string $view_file, array $params = [])
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
   * Set Current page title
   * @method setPageTitle
   * @param string $page_title page title
   * @return null
   */
  public static function setPageTitle(string $page_title)
  {
    self::$page_title = $page_title;
  }
  /**
   * Get Current page title
   * @method getPageTitle
   * @return string
   */
  public static function getPageTitle()
  {
    return self::$page_title;
  }
}
