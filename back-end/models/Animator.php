<?php
require_once '../Database.php';

class Animator {

  static function createTable () {
    Database::process('
      CREATE TABLE IF NOT EXISTS workshops_animators (
        code CHAR(6)               PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE
      );');
  }

  static function getByCode ($code) {
    return Database::fetch(
      'SELECT *
         FROM workshops_animators
        WHERE code = :code;',
      ['code' => $code]
    )[0];
  }
}
