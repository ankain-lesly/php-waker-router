<?php

/**
 * User: Dev_Lee
 * Date: 06/29/2023 - Time: 6:00 AM
 * Updated: 10/03/2023 - Time: 9:30 PM
 * Updated: 10/06/2023 - Time: 10:00 AM
 */

namespace Devlee\WakerRouter\Services;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-router
 */

trait TemplatesTrait
{

  /**
   * Display the Content|String|Text|html provided without layout
   * @method content
   * @param string $content Content to render on page
   * @param string $page_title Set the title of the page
   * @return null
   */
  public function content(string $content, string $page_title = null)
  {
    if ($page_title) {
      $this->setPageTitle($page_title);
    }
    $this->viewHandler->content($content);
  }
  /**
   * Load a view template
   * @method load
   * @param string $view Template|view name to be rendered
   * @param string $page_title Set the title of the page
   * @param array $context An array|object of data to be parsed to the view
   * @return null
   */
  public function load(string $view, string $page_title = null, array $context = [])
  {
    $this->viewHandler->load($view, $page_title, $context);
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
    $this->viewHandler->render($view, $page_title, $context);
  }

  /**
   * Set current page title
   * @method setPageTitle
   * @param string $page_title Set the title of the page
   * @return null
   */
  public function setPageTitle($page_title)
  {
    $this->viewHandler::setPageTitle($page_title);
  }
}
