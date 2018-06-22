<?php
define('CSV_DELIMITER', ';');
define('CSV_ENCLOSURE', '"');

class CsvUtils {

  public static function fromObjects ($objects, $columnNames) {
    if (count($objects) === 0) {
      $objects = [new StdClass];
    }
    $grid = [];
    $keys = array_keys(get_object_vars($objects[0]));
    $renamedKeys = array_map(function ($key) use ($columnNames) {
      return $columnNames[$key];
    }, $keys);
    $grid[] = $renamedKeys;
    array_walk($objects, function ($object) use (&$grid, $keys) {
      $grid[] = array_map(function ($key) use ($object) {
        return $object->$key;
      }, $keys);
    });
    return implode("\n", array_map(function ($row) {
      return implode(CSV_DELIMITER, array_map(function ($value) {
        $value = str_replace(
          CSV_ENCLOSURE,
          CSV_ENCLOSURE . CSV_ENCLOSURE,
          $value
        );
        return CSV_ENCLOSURE . $value . CSV_ENCLOSURE;
      }, $row));
    }, $grid));
  }
}
