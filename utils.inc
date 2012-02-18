<?php
// Convert a date/time dd.mm.yy / hh:min to s since EPOCH time 
// adjusted to timezone
function convertToEpoch($timestamp) {
   list($date, $time) = explode("/", $timestamp);
   list($day, $month, $year) = explode(".", $date);
   list($hour, $min) = explode(":", $time);
   $t = mktime($hour, $min, 0, $month, $day, $year, -1);
   // +date("Z") is used to realigned UTC time to server timezone
   $t = $t + date("Z");
   return ($t);
}

function converToEpochMS($timestamp) {
   // give offset from EPOCH in current timezone
   $t = convertToEpoch($timestamp);
   $t = $t * 1000; // in ms
   return ($t);
}

// adjust date/time from log file to current timezone
function ajustTimestampToCurrentTimezone($timestamp) {
   $offsetEpoch = convertToEpoch($timestamp);
   // reformat date time
   $reformattedDate = date("d.m.y/H:i", $offsetEpoch);
   return $reformattedDate;
}
?>
