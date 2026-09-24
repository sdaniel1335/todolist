<?php

return array(
  'GET' => array(
    '/' => array(
      'controller' => 'pages',
      'class' => 'Pages',
      'action' => 'index'
    ),
    '/login' => array(
      'controller' => 'auth',
      'class' => 'Auth',
      'action' => 'login'
    ),
    '/password' => array(
      'controller' => 'auth',
      'class' => 'Auth',
      'action' => 'password'
    ),
    '/todoist' => array(
      'controller' => 'settings',
      'class' => 'Settings',
      'action' => 'todoist'
    )
  ),
  'POST' => array(
    '/login' => array(
      'controller' => 'auth',
      'class' => 'Auth',
      'action' => 'loginPost'
    ),
    '/logout' => array(
      'controller' => 'auth',
      'class' => 'Auth',
      'action' => 'logout'
    ),
    '/password' => array(
      'controller' => 'auth',
      'class' => 'Auth',
      'action' => 'passwordPost'
    ),
    '/todoist' => array(
      'controller' => 'settings',
      'class' => 'Settings',
      'action' => 'todoistPost'
    ),
    '/todoist/task' => array(
      'controller' => 'todoist',
      'class' => 'Todoist',
      'action' => 'taskPost'
    ),
    '/todolist/task' => array(
      'controller' => 'tasks',
      'class' => 'Tasks',
      'action' => 'todolistPost'
    )
  )
);
