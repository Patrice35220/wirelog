<?php
// Convert a date/time dd.mm.yy / hh:min to ms since EPOCH time 
function converToEpochMS($timestamp) {
   list($date, $time) = explode("/", $timestamp);
   list($day, $month, $year) = explode(".", $date);
   list($hour, $min) = explode(":", $time);
   $t = mktime($hour, $min, 0, $month, $day, $year, -1);
   // +date("Z") is used to realigned UTC time to server timezone
   $t = $t + date("Z");
   $t = $t * 1000; // in ms
   return ($t);
}
?>
