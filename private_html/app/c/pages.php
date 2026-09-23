<?php
class Pages extends App {
  public function __construct() {
    parent::__construct();
  }

  public function index() {
    /*
    $result = $this->db->query("SELECT * FROM posts LIMIT 10");
    var_dump($result->fetch_all(MYSQLI_ASSOC));
    */

    // $this->flash('success', 'Saved successfully.');

    $this->set('title', 'App');
    $this->render('pages/index');
  }
}