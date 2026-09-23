<?php
// /cli.php?file=test
define('FAKE_CLI', true);

$file = basename(
  isset($_GET['file'])
    ? $_GET['file']
    : ''
);

if ($file === '') {
  die('Missing file parameter.');
}

$argv = array(
  $file . '.php'
);

foreach ($_GET as $key => $value) {
  if ($key === 'file') {
    continue;
  }

  if (is_array($value)) {
    continue;
  }

  $argv[] = '--' . $key . '=' . $value;
}

$argc = count($argv);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'private_html' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . $file . '.php';

