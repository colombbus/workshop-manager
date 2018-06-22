<?php

class BadRequestException extends Exception {

  function __construct ($message) {
    parent::__construct($message);
  }
}
