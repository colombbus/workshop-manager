<?php
require_once '../exceptions/BadRequestException.php';
require_once '../models/Report.php';
require_once '../models/Animator.php';
require_once '../utils/CsvUtils.php';
require_once '../utils/ObjectUtils.php';

class ReportController {

  static function create ($body) {
    self::validateInput($body);
    $animator = Animator::getByCode($body->animatorCode);
    unset($body->animatorCode);
    $body->animatorName = $animator->name;
    Report::create($body);
    self::notifyCreation($body);
  }

  static function download () {
    $reports = Report::getAll();
    $csv = CsvUtils::fromObjects($reports, [
      'id' => 'id',
      'animatorName' => 'animateur',
      'type' => 'type',
      'establishment' => 'établissement',
      'fromTime' => 'début',
      'toTime' => 'fin',
      'participantCount' => 'nombre de participants',
      'activitiesDetails' => 'détails des activités',
      'appreciationRating' => 'appréciation',
      'appreciationDetails' => 'détails de l\'appréciation',
      'hadBugs' => 'a rencontré des bugs',
      'bugsDetails' => 'détails des bugs',
      'remarks' => 'remarques'
    ]);
    header('Content-type: text/csv; charset=utf-8');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    echo $csv;
    // var_dump($csv);
  }

  static function validateInput ($data) {
    $allowedKeys = [
      'animatorCode', 'type', 'establishment', 'fromTime', 'toTime',
      'participantCount', 'activitiesDetails', 'appreciationRating',
      'appreciationDetails', 'hadBugs', 'bugsDetails', 'remarks'
    ];
    foreach ($allowedKeys as $key) {
      if (!isset($data->$key)) {
        throw new BadRequestException("The required property $key is missing.");
      }
    }
  }

  private static function notifyCreation ($data) {
    $content = file_get_contents(__DIR__ . '/report-mail.html');
    foreach ($data as $key => $value) {
      $content = str_replace('$' . $key, htmlentities($value), $content);
    }
    $subject = 'nouvelle séance Declick';
    $headers = 'From: Colombbus <tap@colombbus.org>' . "\r\n" .
               'Content-Type: text/html; charset=utf-8' . "\r\n";
    $tos = [
      'guillaume.macquat@colombbus.org',
      'remi.garnier@colombbus.org'
    ];
    foreach ($tos as $to) {
      mail($to, $subject, $content, $headers);
    }
  }
}
