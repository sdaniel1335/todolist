<?php

$routes_file = ROOT . DS . PRIV . DS . APP . DS . 'routes.php';

if ( ! file_exists($routes_file)) {
  die('The routes file does not exist.');
}

$framework_view_file = ROOT
  . DS . PRIV
  . DS . FW
  . DS . 'v.php';

if ( ! file_exists($framework_view_file)) {
  die('The framework view file does not exist.');
}

require_once $framework_view_file;

$framework_controller_file = ROOT
  . DS . PRIV
  . DS . FW
  . DS . 'c.php';

if ( ! file_exists($framework_controller_file)) {
  die('The framework controller file does not exist.');
}

require_once $framework_controller_file;

$app_controller_file = ROOT
  . DS . PRIV
  . DS . APP
  . DS . 'c'
  . DS . '_app.php';

if ( ! file_exists($app_controller_file)) {
  die('The app controller file does not exist.');
}

require_once $app_controller_file;

$routes = require $routes_file;

$request_method = isset($_SERVER['REQUEST_METHOD'])
  ? $_SERVER['REQUEST_METHOD']
  : 'GET';

$request_uri = isset($_SERVER['REQUEST_URI'])
  ? $_SERVER['REQUEST_URI']
  : '/';

$request_path = parse_url(
  $request_uri,
  PHP_URL_PATH
);

if (
  BASE_URL !== ''
  && (
    $request_path === BASE_URL
    || strpos($request_path, BASE_URL . '/') === 0
  )
) {
  $request_path = substr(
    $request_path,
    strlen(BASE_URL)
  );
}

$request_path = '/' . trim($request_path, '/');

if ($request_path !== '/') {
  $request_path = rtrim($request_path, '/');
}

$route = isset($routes[$request_method][$request_path])
  ? $routes[$request_method][$request_path]
  : null;

if ($route === null) {
  header('HTTP/1.1 404 Not Found');
  die('Page not found.');
}

if (
  ! isset(
    $route['controller'],
    $route['class'],
    $route['action']
  )
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('Invalid route configuration.');
}

$controller_file = ROOT
  . DS . PRIV
  . DS . APP
  . DS . 'c'
  . DS . $route['controller']
  . '.php';

if ( ! file_exists($controller_file)) {
  header('HTTP/1.1 500 Internal Server Error');
  die('Controller file not found.');
}

require_once $controller_file;

$controller_class = $route['class'];
$controller_action = $route['action'];

if ( ! class_exists($controller_class)) {
  header('HTTP/1.1 500 Internal Server Error');
  die('Controller class not found.');
}

if ( ! is_subclass_of($controller_class, 'App')) {
  header('HTTP/1.1 500 Internal Server Error');
  die('Controller must extend App.');
}

$controller = new $controller_class();

if (
  substr($controller_action, 0, 2) === '__'
  || ! is_callable(array($controller, $controller_action))
) {
  header('HTTP/1.1 500 Internal Server Error');
  die('Controller action not found.');
}

$controller->{$controller_action}();
