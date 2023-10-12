<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\PHPRouter\Services;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  php-router-core
 */

trait TemplatesTrait
{

  /**
   * Display the Content|String|Text|html provided without layout
   * @method content
   * @param string $text Text to render on page
   * @param string $title Page title
   * @return null
   */
  public function content(string $text, string $title = null)
  {
    if ($title) {
      $this->setPageTitle($title);
    }
    $this->viewHandler->content($text);
  }
  /**
   * Load a view template
   * @method load
   * @param string $view Template view to render
   * @param string $title Page title
   * @param array $context Data to be parsed to view
   * @return null
   */
  public function load(string $view, array $context = [], string $title = null)
  {
    if ($title) {
      $this->setPageTitle($title);
    }
    $this->viewHandler->load($view, $context);
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
    $this->viewHandler->render($view, $context);
  }

  /**
   * Set current page title
   * @method setPageTitle
   * @param string $title Page title
   * @return null
   */
  public function setPageTitle($title)
  {
    $this->viewHandler::setPageTitle($title);
  }
}
