<?php
require_once '../Database.php';

class Report {

  static function createTable () {
    Database::process("
      CREATE TABLE IF NOT EXISTS workshops_reports (
        id                   INT                UNSIGNED AUTO_INCREMENT
                                                PRIMARY KEY,
        animator_name        VARCHAR(255)       NOT NULL,
        type                 ENUM('TAP', 'collège')
                                                NOT NULL,
        establishment        VARCHAR(255)       NOT NULL,
        from_time            DATETIME           NOT NULL,
        to_time              DATETIME           NOT NULL,
        participant_count    INT                NOT NULL,
        activities_details   TEXT               NOT NULL,
        appreciation_rating  ENUM('mauvaise', 'normale', 'bonne')
                                                NOT NULL,
        appreciation_details TEXT               NOT NULL,
        had_bugs             ENUM('oui', 'non') NOT NULL,
        bugs_details         TEXT               NOT NULL,
        remarks              TEXT               NOT NULL
      );");
  }

  static function create ($data) {
    $result = Database::process('
      INSERT INTO workshops_reports (animator_name, type, establishment,
        from_time, to_time, participant_count, activities_details, appreciation_rating,
        appreciation_details, had_bugs, bugs_details, remarks)
      VALUES (:animatorName, :type, :establishment, :fromTime, :toTime,
        :participantCount, :activitiesDetails, :appreciationRating,
        :appreciationDetails, :hadBugs, :bugsDetails, :remarks);
    ', $data);
  }

  static function getAll () {
    return Database::fetch('SELECT * FROM workshops_reports;');
  }
}
