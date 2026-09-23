<?php

if (PHP_SAPI !== 'cli' && (!defined('FAKE_CLI') || !FAKE_CLI)) {
  die('CLI only.');
}

require_once __DIR__
  . DIRECTORY_SEPARATOR
  . 'bootstrap.php';

if (
  defined('FAKE_CLI')
  && FAKE_CLI
  && defined('APP_ENV')
  && APP_ENV === 'prod'
) {
  header('HTTP/1.1 403 Forbidden');
  die('Web-based CLI execution is disabled in production.');
}

function api_date_to_mysql($date, $milliseconds = false)
{
  if ($date === null || $date === '') {
    return null;
  }

  try {
    $date_time = new DateTime($date);

    $date_time->setTimezone(
      new DateTimeZone(APP_TZ)
    );

    if ($milliseconds) {
      return substr(
        $date_time->format('Y-m-d H:i:s.u'),
        0,
        -3
      );
    }

    return $date_time->format('Y-m-d H:i:s');

  } catch (Exception $e) {
    return null;
  }
}

$db = db();

if (!isset($log) || $log !== false) {
  $microtime = explode(' ', microtime());

  $run_id = time()
    . substr($microtime[0], 2, 6)
    . sprintf('%u', crc32(basename($_SERVER['SCRIPT_FILENAME'], '.php')))
    . getmypid();

  $log_dir = ROOT
    . DS . PRIV
    . DS . LOG
    . DS . 'cli'
    . DS . basename($_SERVER['SCRIPT_FILENAME'], '.php')
    . DS . date('Y')
    . DS . date('m')
    . DS . date('d')
    . DS;

  if (!is_dir($log_dir)) {
    if (!mkdir($log_dir, 0777, true) && !is_dir($log_dir)) {
      die('Log directory creation failed.');
    }
  }

  $log_path = $log_dir . $run_id . '.log';

  if (file_put_contents($log_path, $run_id . PHP_EOL . PHP_EOL) === false) {
    die('Log write failed.');
  }
}

function log_write($content)
{
  global $log, $log_path;

  if (isset($log) && $log === false) {
    return false;
  }

  if (!isset($log_path)) {
    return false;
  }

  return file_put_contents(
      $log_path,
      $content . PHP_EOL,
      FILE_APPEND
    ) !== false;
}