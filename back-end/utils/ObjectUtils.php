<?php

class ObjectUtils {

  static function keysToCamelCase ($data) {
    $result = new StdClass;
    foreach ($data as $key => $value) {
      $newKey = self::toCamelcase($key);
      $result->$newKey = $value;
    }
    return $result;
  }

  private static function toCamelcase ($text) {
    $parts = explode('_', $text);
    $result = $parts[0];
    foreach (array_slice($parts, 1) as $part) {
      $result .= ucfirst($part);
    }
    return $result;
  }
}
