<?php
require_once '../models/Animator.php';
require_once '../models/Report.php';

class Application {
  public static function install () {
    Animator::createTable();
    Report::createTable();
  }
}
