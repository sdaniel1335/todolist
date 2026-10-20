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

    try {
      if ($action === 'create') {
        $this->createLocalTask(
          $user_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', lang('TODOLIST_TASK_ADDED'));
      } elseif ($task_id < 1) {
        throw new RuntimeException(lang('MISSING_TODOLIST_TASK_ID'));
      } elseif ($action === 'delete') {
        $this->deleteLocalTask($task_id, $user_id);
        $this->flash('success', lang('TODOLIST_TASK_DELETED'));
      } elseif ($action === 'update') {
        $this->updateLocalTask(
          $task_id,
          $user_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', lang('TODOLIST_TASK_UPDATED'));
      } elseif ($action === 'to_todoist') {
        $api_key = $this->getTodoistApiKey($user_id);

        if ($api_key === '') {
          throw new RuntimeException(lang('SET_YOUR_TODOIST_API_KEY_FIRST'));
        }

        $this->moveTodolistToTodoist(
          new Todoist($api_key),
          $task_id,
          $user_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', lang('TASK_MOVED_TO_TODOIST_TODAY'));
      } else {
        $this->flash('error', lang('UNKNOWN_TODOLIST_ACTION'));
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
        throw new RuntimeException(lang('TODOLIST_TASK_NOT_FOUND'));
      }

      $created = $api->createTask($content, $label, true);

      if (!is_array($created) || !isset($created['id'])) {
        throw new RuntimeException(lang('TODOIST_DID_NOT_RETURN_THE_CREATED_TASK_ID'));
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
        throw new RuntimeException(lang('TODOLIST_TASK_COULD_NOT_BE_REMOVED_AFTER_MOVING'));
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
            . lang('THE_CREATED_TODOIST_TASK_COULD_NOT_BE_CLEANED_UP_AUTOMATICALLY')
          );
        }
      }

      throw $e;
    }
  }

  private function createLocalTask($user_id, $content, $label)
  {
    $stmt = $this->db->prepare(
      'INSERT INTO '
      . $this->table('todolist')
      . ' (user_id, content, label) VALUES (?, ?, ?)'
    );
    $stmt->bind_param('iss', $user_id, $content, $label);
    $stmt->execute();
    $stmt->close();
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
      throw new RuntimeException(lang('TODOLIST_TASK_NOT_FOUND'));
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
        throw new RuntimeException(lang('TODOLIST_TASK_NOT_FOUND'));
      }
    }

    $stmt->close();
  }

  private function postContent()
  {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';

    if ($content === '') {
      throw new RuntimeException(lang('TASK_CONTENT_IS_REQUIRED'));
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
      throw new RuntimeException(lang('INVALID_TASK_LABEL'));
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
