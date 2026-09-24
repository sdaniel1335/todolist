<?php

require_once __DIR__
  . DIRECTORY_SEPARATOR
  . 'todoist.php';

class Pages extends App
{
  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $user_id = auth_user_id();
    $todoist_tasks = array();
    $todoist_today_ids = array();
    $todoist_error = '';
    $todoist = new Todoist();
    $has_todoist_api_key = $todoist->hasApiKey();

    if ($has_todoist_api_key) {
      try {
        $todoist_tasks = $todoist->getTasks();
        $today_tasks = $todoist->getTodayTasks();

        foreach ($today_tasks as $task) {
          if (isset($task['id'])) {
            $todoist_today_ids[(string) $task['id']] = true;
          }
        }

        $today_first = array();
        $other_tasks = array();

        foreach ($todoist_tasks as $task) {
          $task['_managed_label'] = $todoist->taskLabel($task);

          if (
            isset($task['id'])
            && isset($todoist_today_ids[(string) $task['id']])
          ) {
            $today_first[] = $task;
          } else {
            $other_tasks[] = $task;
          }
        }

        $todoist_tasks = array_merge($today_first, $other_tasks);
      } catch (Exception $e) {
        $todoist_error = $e->getMessage();
      }
    }

    $todolist_tasks = array();
    $stmt = $this->db->prepare(
      'SELECT id, content, label, created_at FROM '
      . $this->table('todolist')
      . ' WHERE user_id = ? ORDER BY created_at DESC, id DESC'
    );
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($id, $content, $label, $created_at);

    while ($stmt->fetch()) {
      $todolist_tasks[] = array(
        'id' => (int) $id,
        'content' => $content,
        'label' => $label,
        'created_at' => $created_at
      );
    }

    $stmt->close();

    $this->set(
      array(
        'title' => APP_TITLE,
        'todoist_tasks' => $todoist_tasks,
        'todoist_today_ids' => $todoist_today_ids,
        'todoist_error' => $todoist_error,
        'has_todoist_api_key' => $has_todoist_api_key,
        'todolist_tasks' => $todolist_tasks
      )
    );

    $this->render('pages/index');
  }
}
