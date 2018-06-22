<?php
require_once 'utils/ObjectUtils.php';

define('RDBMS_USERNAME', 'root');
define('RDBMS_PASSWORD', '');
define('RDBMS_HOST', 'localhost');
define('RDBMS_DATABASE', 'workshops');

class Database {

  private static function connect () {
    $connection = new PDO(
      'mysql:dbname=' . RDBMS_DATABASE .
      ';host=' . RDBMS_HOST .
      ';charset=utf8',
      RDBMS_USERNAME,
      RDBMS_PASSWORD,
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
    );
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $connection;
  }

  static function process ($query, $params = []) {
    $connection = self::connect();
    foreach ($params as $key => $value) {
      $query = str_replace(':' . $key, $connection->quote($value), $query);
    }
    $request = $connection->query($query);
    return $request;
  }

  static function fetch ($query, $params = []) {
    $request = self::process($query, $params);
    $results = $request->fetchAll();
    $normalizedResults = [];
    foreach ($results as $result) {
      $normalizedResults[] = ObjectUtils::keysToCamelCase($result);
    }
    return $normalizedResults;
  }
}
