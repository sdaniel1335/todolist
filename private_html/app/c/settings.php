<?php

class Settings extends App
{
  public function todoist()
  {
    $this->requireLogin();

    $user_id = auth_user_id();

    $stmt = $this->db->prepare(
      'SELECT id FROM '
      . $this->table('todoist_accounts')
      . ' WHERE user_id = ? LIMIT 1'
    );

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($account_id);

    $has_api_key = $stmt->fetch();
    $stmt->close();

    $this->set(
      array(
        'title' => 'Todoist API key',
        'has_api_key' => (bool) $has_api_key
      )
    );

    $this->render('settings/todoist');
  }

  public function todoistPost()
  {
    $this->requireLogin();
    $this->requireCsrf();

    $api_key = isset($_POST['api_key'])
      ? trim($_POST['api_key'])
      : '';

    if ($api_key === '') {
      $this->flash('error', 'The Todoist API key is required.');
      $this->redirect('/todoist');
    }

    if (strlen($api_key) > 255) {
      $this->flash('error', 'The Todoist API key is too long.');
      $this->redirect('/todoist');
    }

    $user_id = auth_user_id();

    $stmt = $this->db->prepare(
      'INSERT INTO '
      . $this->table('todoist_accounts')
      . ' (user_id, api_key) VALUES (?, ?)'
      . ' ON DUPLICATE KEY UPDATE api_key = VALUES(api_key)'
    );

    $stmt->bind_param('is', $user_id, $api_key);
    $stmt->execute();
    $stmt->close();

    $this->flash('success', 'Todoist API key saved.');
    $this->redirect('/todoist');
  }
}
