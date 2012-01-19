<?php

namespace Oxygen\UtilityBundle\Utility;

class Utility {
  
  public static $email = 'test@test.com';
  public static $site_email = 'test@test.com';

  public static function randomString($length=12) {
    $string = 'qwertyuiopasdfghjklzxcvbnm1234567890';
    $random = '';
    for ($i = 0; $i < $length; $i++)
      $random .= $string[rand(0, strlen($string) - 1)];
    return $random;
  }
  
  public static function slugify($text, $substitute = '-') {
    $text = preg_replace('/\W+/', $substitute, $text);
    $text = strtolower(trim($text, $substitute));
    return $text;
  }
  
  public static function stripper($val) {
    foreach (array(" ", "&nbsp;", "\n", "\t", "\r") as $strip)
      $val = str_replace($strip, '', (string) $val);
    return $val === '' ? false : $val;
  }

}
