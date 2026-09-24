<?php

class C
{
  protected $db;

  protected $view_vars = array();

  public function __construct()
  {
    $this->db = db();
  }

  protected function table($table)
  {
    return table($table);
  }

  protected function set($key, $value = null)
  {
    if (is_array($key)) {
      foreach ($key as $k => $v) {
        $this->view_vars[$k] = $v;
      }

      return;
    }

    $this->view_vars[$key] = $value;
  }

  protected function flash($type, $message)
  {
    if (!defined('APP_SESSION') || !APP_SESSION) {
      throw new RuntimeException(
        'Flash messages require APP_SESSION to be enabled.'
      );
    }

    $_SESSION['_flash'][] = array(
      'type' => $type,
      'message' => $message
    );
  }

  protected function render($view, $layout = 'layout')
  {
    $data = $this->view_vars;

    $view_file = ROOT
      . DS . PRIV
      . DS . APP
      . DS . 'v'
      . DS . $view
      . '.php';

    if ( ! file_exists($view_file)) {
      header('HTTP/1.1 500 Internal Server Error');
      die('View file not found.');
    }

    extract($data, EXTR_SKIP);

    if ($layout === false) {
      require $view_file;
      return;
    }

    $layout_file = ROOT
      . DS . PRIV
      . DS . APP
      . DS . 'v'
      . DS . $layout
      . '.php';

    if ( ! file_exists($layout_file)) {
      header('HTTP/1.1 500 Internal Server Error');
      die('Layout file not found.');
    }

    ob_start();

    require $view_file;

    $content = ob_get_clean();

    require $layout_file;
  }

  protected function redirect($url)
  {
    header('Location: ' . url($url));
    exit;
  }
}
