<?php


function error($level, $msg) {
  switch ($level) {
  case 1:
    print ($msg);
    break;
    
  case 2:
    header("HTTP/1.1 500 Server Error");
    die($msg);
    break;
  }
}

function t($string) {

}