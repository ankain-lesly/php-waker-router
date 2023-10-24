<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter;

use Devlee\WakerRouter\Exceptions\TemplateEnginException;
use Devlee\WakerRouter\Exceptions\ViewNotFoundException;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

class View
{
  /**
   *  Template Engine
   */
  private ?object $engine = null;

  /**
   *  configs
   */
  private static string $views_path;
  private static ?string $LAYOUT_DIR = null;
  // private static ?string $ROOT_PATH = null;

  # page title
  private static string $page_title = "Router: Page title";

  public function __construct(string $views_path)
  {
    self::$views_path = $views_path;
  }

  /**
   * Working with layout Dir get|set
   */
  public static function getLayoutsDir()
  {
    return self::$LAYOUT_DIR;
  }
  public static function setLayoutsDir(string $layout_dir)
  {
    self::$LAYOUT_DIR = $layout_dir;
  }

  /**
   * Display the Content|String|Text|html provided without layout
   * @method content
   * @param string $content Text to render on page
   * @param string $page_title Set the title of the page
   * @return null
   */
  public function content(string $content, ?string $page_title = null)
  {
    if ($page_title) {
      $this->setPageTitle($page_title);
    }
    $view = $content;
    $pageContent = $this->getLayoutContent($view);

    exit($pageContent);
  }

  /**
   * Load a view template
   * @method load
   * @param string $view Template|view name to be rendered
   * @param string $page_title Set the title of the page
   * @param array $context An array|object of data to be parsed to the view
   * @return null
   */
  public function load(string $view, ?string $page_title = null, array $context = [])
  {
    if ($page_title) {
      $this->setPageTitle($page_title);
    }
    $view = $this->getViewContent($view, $context);
    exit($view);
  }

  /**
   * Load a view|template within a main layout if available
   * @method render
   * @param string $view Template|view name to be rendered
   * @param string $page_title Set the title of the page
   * @param array $context An array|object of data to be parsed to the view
   * @return null
   */
  public function render(string $view, ?string $page_title = null, array $context = [])
  {
    if ($page_title) {
      $this->setPageTitle($page_title);
    }
    # Load Data into view
    $view = $this->getViewContent($view, $context);
    $pageContent = $this->getLayoutContent($view);

    exit($pageContent);
  }

  /**
   * Load a view content
   * @method getViewContent
   */
  private function getViewContent(string $view, $params = [])
  {
    $file = self::$views_path . '/' . $view;

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
   * Load layout content
   * @method getLayoutContent
   * @return null
   */
  private function getLayoutContent($view)
  {
    $layout_view = self::$LAYOUT_DIR;

    if (!$layout_view) return $view;
    $pageContent = "";

    $file = self::$views_path . '/' . $layout_view;

    if (file_exists($file . ".php"))
      $pageContent = self::loadViewTemplate($file . ".php");
    elseif (file_exists($file . ".html"))
      $pageContent = self::loadViewTemplate($file . ".html");
    else {
      throw new ViewNotFoundException("Layout", $file);
    }

    $pageContent = str_replace("{{content}}", $view, $pageContent);
    $pageContent = str_replace("{{page_title}}", $this->getPageTitle(), $pageContent);
    return $pageContent;
  }

  /**
   * @method LoadTwigTemplate
   * Load a twig template
   */
  private function LoadTwigTemplate(string $view_file, array $params = [])
  {
    if (!$this->engine)
      throw new TemplateEnginException('Error loading template engine to render application templates...');

    return $this->engine->render($view_file, $params);
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

  /**
   * Set Template Engine
   */
  public function setEngine(object $engine)
  {
    $this->engine = $engine;
  }
}
