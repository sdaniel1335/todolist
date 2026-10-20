<?php

class Auth extends App
{
  public function __construct()
  {
    parent::__construct(true);
  }

  public function login()
  {
    if (auth_logged_in()) {
      $this->redirect('/');
    }

    $this->set('title', lang('LOGIN'));
    $this->render('auth/login');
  }

  public function loginPost()
  {
    if (auth_logged_in()) {
      $this->redirect('/');
    }

    $this->requireCsrf();

    $username = isset($_POST['username'])
      ? trim($_POST['username'])
      : '';

    $password = isset($_POST['password'])
      ? $_POST['password']
      : '';

    if ($username === '' || $password === '') {
      $this->flash('error', lang('USERNAME_AND_PASSWORD_ARE_REQUIRED'));
      $this->redirect('/login');
    }

    $stmt = $this->db->prepare(
      'SELECT id, username, password_hash FROM '
      . $this->table('users')
      . ' WHERE username = ? LIMIT 1'
    );

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $db_username, $password_hash);

    $valid = false;

    if ($stmt->fetch()) {
      $valid = auth_password_check($password, $password_hash);
    }

    $stmt->close();

    if (!$valid) {
      $this->flash('error', lang('INVALID_USERNAME_OR_PASSWORD'));
      $this->redirect('/login');
    }

    session_regenerate_id(true);

    $_SESSION['auth_user_id'] = (int) $id;
    $_SESSION['auth_username'] = $db_username;

    $this->flash('success', lang('LOGGED_IN'));
    $this->redirect('/');
  }

  public function logout()
  {
    $this->requireLogin();
    $this->requireCsrf();

    unset(
      $_SESSION['auth_user_id'],
      $_SESSION['auth_username'],
      $_SESSION['_auth_csrf']
    );

    session_regenerate_id(true);

    $this->flash('success', lang('LOGGED_OUT'));
    $this->redirect('/login');
  }

  public function password()
  {
    $this->requireLogin();

    $this->set('title', lang('CHANGE_PASSWORD'));
    $this->render('auth/password');
  }

  public function passwordPost()
  {
    $this->requireLogin();
    $this->requireCsrf();

    $current_password = isset($_POST['current_password'])
      ? $_POST['current_password']
      : '';

    $new_password = isset($_POST['new_password'])
      ? $_POST['new_password']
      : '';

    $new_password_confirm = isset($_POST['new_password_confirm'])
      ? $_POST['new_password_confirm']
      : '';

    if (
      $current_password === ''
      || $new_password === ''
      || $new_password_confirm === ''
    ) {
      $this->flash('error', lang('ALL_PASSWORD_FIELDS_ARE_REQUIRED'));
      $this->redirect('/password');
    }

    if (strlen($new_password) < 8) {
      $this->flash('error', lang('THE_NEW_PASSWORD_MUST_BE_AT_LEAST_8_CHARACTERS'));
      $this->redirect('/password');
    }

    if ($new_password !== $new_password_confirm) {
      $this->flash('error', lang('THE_NEW_PASSWORDS_DO_NOT_MATCH'));
      $this->redirect('/password');
    }

    $user_id = auth_user_id();

    $stmt = $this->db->prepare(
      'SELECT password_hash FROM '
      . $this->table('users')
      . ' WHERE id = ? LIMIT 1'
    );

    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($password_hash);

    $found = $stmt->fetch();
    $stmt->close();

    if (!$found || !auth_password_check($current_password, $password_hash)) {
      $this->flash('error', lang('CURRENT_PASSWORD_IS_INCORRECT'));
      $this->redirect('/password');
    }

    $new_hash = auth_password_make($new_password);

    $stmt = $this->db->prepare(
      'UPDATE '
      . $this->table('users')
      . ' SET password_hash = ? WHERE id = ?'
    );

    $stmt->bind_param('si', $new_hash, $user_id);
    $stmt->execute();
    $stmt->close();

    session_regenerate_id(true);

    $this->flash('success', lang('PASSWORD_CHANGED'));
    $this->redirect('/password');
  }
}
