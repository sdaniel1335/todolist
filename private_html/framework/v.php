<?php

function url($path = '')
{
  $base = BASE_URL;

  if (defined('APP_URL') && APP_URL !== '') {
    $base = rtrim(APP_URL, '/') . BASE_URL;
  }

  if ($path === '' || $path === '/') {
    return $base . '/';
  }

  return $base . '/' . ltrim($path, '/');
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
  if (!defined('APP_SESSION') || !APP_SESSION) {
    return array();
  }

  $flashes = isset($_SESSION['_flash'])
    ? $_SESSION['_flash']
    : array();

  unset($_SESSION['_flash']);

  return $flashes;
}
