<?php
error_reporting(0);
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';

$num = "11111209";

$classname = "Numbers_Words_Locale_en_IN" ;
  $obj = new $classname;

  try {
    $ret = $obj->toWords($num);
    echo $ret;
  } catch (Numbers_Words_Exception $nwe) {
    echo (string)$nwe . "\n";
  }

