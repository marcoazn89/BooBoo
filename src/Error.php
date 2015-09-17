<?php
namespace Exception;

class Error {
  public static $errors = [];

  public static function getDescription($errorNumer) {
    return self::$errors[$errorNumer];
  }

  public static function getNumber($error) {
    return array_search($error, self::$errors);
  }

  public static function registerErrors(array $errors) {
    if(count($merge = array_merge(self::$errors, $errors)) !==
      count(self::$errors) + count($errors)) {
      throw \Exception('Unable to register errors', 1);
    }

    self::$errors = $merge;
  }
}