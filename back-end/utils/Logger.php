<?php

class Logger {

  static function error ($message) {
    file_put_contents(
      '../logs.txt',
      $message . PHP_EOL . PHP_EOL,
      FILE_APPEND | LOCK_EX
    );
  }
}
