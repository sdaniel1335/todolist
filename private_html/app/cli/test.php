<?php
// /cli.php?file=test&action=insert
// test.php --action=insert >/dev/null 2>&1
// $log = false;
require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cli.php';

$options = getopt('', array(
  'action:'
));

if (!isset($options['action']) || trim($options['action']) === '') {
  die("Missing required parameter: --action\n");
}

if ($options['action'] == 'insert') {
  $db->query("INSERT INTO posts () VALUES ()");

  $id = $db->insert_id;

  log_write($id);
}