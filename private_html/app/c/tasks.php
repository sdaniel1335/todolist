<?php

require_once __DIR__
  . DIRECTORY_SEPARATOR
  . 'todoist.php';

class Tasks extends App
{
  public function todolistPost()
  {
    $this->requireLogin();
    $this->requireCsrf();

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $task_id = isset($_POST['task_id']) ? (int) $_POST['task_id'] : 0;
    $user_id = auth_user_id();

    if ($task_id < 1) {
      $this->flash('error', 'Missing Todolist task ID.');
      $this->redirect('/');
    }

    try {
      if ($action === 'delete') {
        $this->deleteLocalTask($task_id, $user_id);
        $this->flash('success', 'Todolist task deleted.');
      } elseif ($action === 'update') {
        $this->updateLocalTask(
          $task_id,
          $user_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', 'Todolist task updated.');
      } elseif ($action === 'to_todoist') {
        $api_key = $this->getTodoistApiKey($user_id);

        if ($api_key === '') {
          throw new RuntimeException('Set your Todoist API key first.');
        }

        $this->moveTodolistToTodoist(
          new Todoist($api_key),
          $task_id,
          $user_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', 'Task moved to Todoist Today.');
      } else {
        $this->flash('error', 'Unknown Todolist action.');
      }
    } catch (Exception $e) {
      $this->flash('error', $e->getMessage());
    }

    $this->redirect('/');
  }

  private function moveTodolistToTodoist(
    $api,
    $task_id,
    $user_id,
    $content,
    $label
  ) {
    $created_todoist_id = '';
    $this->db->autocommit(false);

    try {
      $stmt = $this->db->prepare(
        'SELECT id FROM '
        . $this->table('todolist')
        . ' WHERE id = ? AND user_id = ? LIMIT 1 FOR UPDATE'
      );
      $stmt->bind_param('ii', $task_id, $user_id);
      $stmt->execute();
      $stmt->bind_result($existing_id);
      $found = $stmt->fetch();
      $stmt->close();

      if (!$found) {
        throw new RuntimeException('Todolist task not found.');
      }

      $created = $api->createTask($content, $label, true);

      if (!is_array($created) || !isset($created['id'])) {
        throw new RuntimeException('Todoist did not return the created task ID.');
      }

      $created_todoist_id = (string) $created['id'];

      $stmt = $this->db->prepare(
        'DELETE FROM '
        . $this->table('todolist')
        . ' WHERE id = ? AND user_id = ?'
      );
      $stmt->bind_param('ii', $task_id, $user_id);
      $stmt->execute();

      if ($stmt->affected_rows !== 1) {
        $stmt->close();
        throw new RuntimeException('Todolist task could not be removed after moving.');
      }

      $stmt->close();
      $this->db->commit();
      $this->db->autocommit(true);
    } catch (Exception $e) {
      $this->db->rollback();
      $this->db->autocommit(true);

      if ($created_todoist_id !== '') {
        try {
          $api->deleteTask($created_todoist_id);
        } catch (Exception $cleanup_exception) {
          throw new RuntimeException(
            $e->getMessage()
            . ' The created Todoist task could not be cleaned up automatically.'
          );
        }
      }

      throw $e;
    }
  }

  private function deleteLocalTask($task_id, $user_id)
  {
    $stmt = $this->db->prepare(
      'DELETE FROM '
      . $this->table('todolist')
      . ' WHERE id = ? AND user_id = ?'
    );
    $stmt->bind_param('ii', $task_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows !== 1) {
      $stmt->close();
      throw new RuntimeException('Todolist task not found.');
    }

    $stmt->close();
  }

  private function updateLocalTask(
    $task_id,
    $user_id,
    $content,
    $label
  ) {
    $stmt = $this->db->prepare(
      'UPDATE '
      . $this->table('todolist')
      . ' SET content = ?, label = ? WHERE id = ? AND user_id = ?'
    );
    $stmt->bind_param('ssii', $content, $label, $task_id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
      $check = $this->db->prepare(
        'SELECT id FROM '
        . $this->table('todolist')
        . ' WHERE id = ? AND user_id = ? LIMIT 1'
      );
      $check->bind_param('ii', $task_id, $user_id);
      $check->execute();
      $check->bind_result($existing_id);
      $exists = $check->fetch();
      $check->close();

      if (!$exists) {
        $stmt->close();
        throw new RuntimeException('Todolist task not found.');
      }
    }

    $stmt->close();
  }

  private function postContent()
  {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if ($content === '') {
      throw new RuntimeException('Task content is required.');
    }

    return $content;
  }

  private function postLabel()
  {
    $label = isset($_POST['label'])
      ? strtoupper(trim($_POST['label']))
      : '';

    if ($label === '') {
      return '';
    }

    if (!in_array($label, array('B', 'P', 'W'), true)) {
      throw new RuntimeException('Invalid task label.');
    }

    return $label;
  }

  private function getTodoistApiKey($user_id)
  {
    $stmt = $this->db->prepare(
      'SELECT api_key FROM '
      . $this->table('todoist_accounts')
      . ' WHERE user_id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($api_key);

    $value = $stmt->fetch() ? $api_key : '';
    $stmt->close();

    return $value;
  }
}
