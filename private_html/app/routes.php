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
    )
  )
);
