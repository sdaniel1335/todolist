<?php

class Todoist extends App
{
  private $api_key = '';
  private $base_url = 'https://api.todoist.com/api/v1';

  public function __construct($api_key = null)
  {
    parent::__construct();

    if ($api_key === null) {
      $api_key = $this->loadApiKey(auth_user_id());
    }

    $this->api_key = trim((string) $api_key);
  }

  public function hasApiKey()
  {
    return $this->api_key !== '';
  }

  public function taskPost()
  {
    $this->requireLogin();
    $this->requireCsrf();

    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $task_id = isset($_POST['task_id']) ? trim($_POST['task_id']) : '';

    if ($task_id === '') {
      $this->flash('error', 'Missing Todoist task ID.');
      $this->redirect('/');
    }

    if (!$this->hasApiKey()) {
      $this->flash('error', 'Set your Todoist API key first.');
      $this->redirect('/todoist');
    }

    try {
      if ($action === 'complete') {
        $this->closeTask($task_id);
        $this->flash('success', 'Todoist task completed.');
      } elseif ($action === 'delete') {
        $this->deleteTask($task_id);
        $this->flash('success', 'Todoist task deleted.');
      } elseif ($action === 'update') {
        $this->updateTask(
          $task_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', 'Todoist task updated.');
      } elseif ($action === 'to_todolist') {
        $this->moveToTodolist(
          $task_id,
          $this->postContent(),
          $this->postLabel()
        );
        $this->flash('success', 'Task moved to Todolist.');
      } else {
        $this->flash('error', 'Unknown Todoist action.');
      }
    } catch (Exception $e) {
      $this->flash('error', $e->getMessage());
    }

    $this->redirect('/');
  }

  public function getTasks()
  {
    $this->requireApiKey();

    return $this->getPaginated('/tasks', array('limit' => 200));
  }

  public function getTodayTasks()
  {
    $this->requireApiKey();

    return $this->getPaginated(
      '/tasks/filter',
      array(
        'query' => 'today',
        'limit' => 200
      )
    );
  }

  public function getTask($task_id)
  {
    $this->requireApiKey();

    return $this->request(
      'GET',
      '/tasks/' . rawurlencode($task_id)
    );
  }

  public function createTask($content, $label = '', $today = false)
  {
    $this->requireApiKey();

    $data = array(
      'content' => $content
    );

    if ($label !== '') {
      $data['labels'] = array($this->normalizeLabel($label));
    }

    if ($today) {
      $data['due_string'] = 'today';
    }

    return $this->request(
      'POST',
      '/tasks',
      $data
    );
  }

  public function updateTask($task_id, $content, $label = '')
  {
    $this->requireApiKey();

    $task = $this->getTask($task_id);
    $labels = array();

    if (is_array($task) && isset($task['labels']) && is_array($task['labels'])) {
      foreach ($task['labels'] as $current_label) {
        if (!$this->isManagedLabel($current_label)) {
          $labels[] = $current_label;
        }
      }
    }

    $label = $this->normalizeLabel($label);

    if ($label !== '') {
      $labels[] = $label;
    }

    return $this->request(
      'POST',
      '/tasks/' . rawurlencode($task_id),
      array(
        'content' => $content,
        'labels' => $labels
      )
    );
  }

  public function closeTask($task_id)
  {
    $this->requireApiKey();

    return $this->request(
      'POST',
      '/tasks/' . rawurlencode($task_id) . '/close'
    );
  }

  public function deleteTask($task_id)
  {
    $this->requireApiKey();

    return $this->request(
      'DELETE',
      '/tasks/' . rawurlencode($task_id)
    );
  }

  public function taskLabel($task)
  {
    if (!is_array($task) || !isset($task['labels']) || !is_array($task['labels'])) {
      return '';
    }

    foreach ($task['labels'] as $label) {
      if ($this->isManagedLabel($label)) {
        return strtoupper($label);
      }
    }

    return '';
  }

  private function moveToTodolist($task_id, $content, $label)
  {
    $user_id = auth_user_id();
    $this->db->autocommit(false);

    try {
      $stmt = $this->db->prepare(
        'INSERT INTO '
        . $this->table('todolist')
        . ' (user_id, content, label) VALUES (?, ?, ?)'
      );
      $stmt->bind_param('iss', $user_id, $content, $label);
      $stmt->execute();
      $stmt->close();

      $this->deleteTask($task_id);

      $this->db->commit();
      $this->db->autocommit(true);
    } catch (Exception $e) {
      $this->db->rollback();
      $this->db->autocommit(true);
      throw $e;
    }
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
    $label = isset($_POST['label']) ? trim($_POST['label']) : '';

    return $this->normalizeLabel($label);
  }

  private function normalizeLabel($label)
  {
    $label = strtoupper(trim((string) $label));

    if ($label === '') {
      return '';
    }

    if (!in_array($label, array('B', 'P', 'W'), true)) {
      throw new RuntimeException('Invalid task label.');
    }

    return $label;
  }

  private function isManagedLabel($label)
  {
    return in_array(
      strtoupper((string) $label),
      array('B', 'P', 'W'),
      true
    );
  }

  private function loadApiKey($user_id)
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

  private function requireApiKey()
  {
    if (!$this->hasApiKey()) {
      throw new RuntimeException('Set your Todoist API key first.');
    }
  }

  private function getPaginated($path, $params)
  {
    $items = array();
    $cursor = null;

    do {
      $query = $params;

      if ($cursor !== null && $cursor !== '') {
        $query['cursor'] = $cursor;
      }

      $response = $this->request('GET', $path, null, $query);

      if (!isset($response['results']) || !is_array($response['results'])) {
        throw new RuntimeException('Unexpected response from Todoist.');
      }

      foreach ($response['results'] as $item) {
        $items[] = $item;
      }

      $cursor = isset($response['next_cursor'])
        ? $response['next_cursor']
        : null;
    } while ($cursor !== null && $cursor !== '');

    return $items;
  }

  private function request($method, $path, $data = null, $query = array())
  {
    if (!function_exists('curl_init')) {
      throw new RuntimeException('The cURL PHP extension is required for Todoist.');
    }

    $url = $this->base_url . $path;

    if (!empty($query)) {
      $url .= '?' . http_build_query($query, '', '&');
    }

    $ch = curl_init($url);

    $headers = array(
      'Authorization: Bearer ' . $this->api_key,
      'Accept: application/json'
    );

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
      curl_setopt($ch, CURLOPT_POST, true);
    } elseif ($method !== 'GET') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    }

    if ($data !== null) {
      $payload = json_encode($data);

      if ($payload === false) {
        curl_close($ch);
        throw new RuntimeException('Could not encode Todoist request.');
      }

      $headers[] = 'Content-Type: application/json';
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    }

    $body = curl_exec($ch);

    if ($body === false) {
      $error = curl_error($ch);
      curl_close($ch);
      throw new RuntimeException('Todoist request failed: ' . $error);
    }

    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status < 200 || $status >= 300) {
      $message = 'Todoist API returned HTTP ' . $status . '.';
      $decoded_error = json_decode($body, true);

      if (is_array($decoded_error)) {
        if (isset($decoded_error['error']) && is_string($decoded_error['error'])) {
          $message .= ' ' . $decoded_error['error'];
        } elseif (isset($decoded_error['message']) && is_string($decoded_error['message'])) {
          $message .= ' ' . $decoded_error['message'];
        }
      }

      throw new RuntimeException($message);
    }

    if ($body === '' || $body === 'null') {
      return null;
    }

    $decoded = json_decode($body, true);

    if ($decoded === null && trim($body) !== 'null') {
      throw new RuntimeException('Could not decode Todoist response.');
    }

    return $decoded;
  }
}
