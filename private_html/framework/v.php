<?php

function url($path = '')
{
  if ($path === '' || $path === '/') {
    return BASE_URL . '/';
  }

  return BASE_URL . '/' . ltrim($path, '/');
}

function element($element, $data = array())
{
  $element_file = ROOT
    . DS . PRIV
    . DS . APP
    . DS . 'v'
    . DS . '_app'
    . DS . $element
    . '.php';

  if ( ! file_exists($element_file)) {
    header('HTTP/1.1 500 Internal Server Error');
    die('Element file not found.');
  }

  extract($data, EXTR_SKIP);

  require $element_file;
}

function flash()
{
  $flashes = isset($_SESSION['_flash'])
    ? $_SESSION['_flash']
    : array();

  unset($_SESSION['_flash']);

  return $flashes;
}
