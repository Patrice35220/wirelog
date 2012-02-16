<?php

function detectNumberOfSensors($line) {
   $tokens = explode(";", $line);
   if (sizeof($tokens) > 1) {
      return (sizeof($tokens)-1);
   } else {
      return 0;
   }
}

function converTimeToSeconds($timestamp) {
   list($date, $time) = explode("/", $timestamp);
   list($hour, $min) = explode(":", $time);
   return ($hour * 3600 + $min * 60);
}

function converDateTimeToTime($timestamp) {
   list($date, $time) = explode("/", $timestamp);
   return ($time.":00");
}

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

// Return an array, first element is timestamp
function readTemperatureRecord($line) {
   $line = trim($line); // remove end of line
   $res = explode(";", $line);
   return $res; 
}

function readAllRecords($handle) {
   // Results array
   $lines = array();
   $res = array();
   $line = fgets($handle);
   $k = 0;
   $j = 0;
   $expectedNbOfRecords = detectNumberOfSensors($line);
   $expectedNbOfRecords++;
   while ($line) {
      $record = readTemperatureRecord($line);
      //if (sizeof($record) > 1) { 
      if (sizeof($record) == $expectedNbOfRecords) { 
         $numbers = 1; // first record is date != numeric
         foreach($record as $element) {
            if (is_numeric($element)) {
               $numbers++;
            }
         }
         // Record is taken into account only if it contains expectedNbOfRecords numbers;
         // This filter out empty entries sometimes given by sensor
         if ($numbers == $expectedNbOfRecords) {
            $lines[$j] = $record;
            $j++;
         } else {
            print("//numbers = $numbers");
            print("//expectedNbOfRecords = $expectedNbOfRecords\n");
         }
      } else {
         $tt = sizeof($record);
         print("//sizeof(record) = $tt, expectedNbOfRecords=$expectedNbOfRecords\n");
      }
      $line = fgets($handle);
   }
   if (sizeof($lines) > 1) {
      // make timestamp array, and one array per column
      $first=true;
      $nbOfColumns = 0; 
      foreach($lines as $line) {
         $nbOfColumns = sizeof($line);
         for($i=0; $i<$nbOfColumns; $i++) {
            if ($i==0) {
               $val = converToEpochMS($line[$i]);
            } else {
               $val = $line[$i];
            }
            if ($first) {
               $res[$i] = $res[$i].$val;
            } else {
               $res[$i] = $res[$i].",".$val;
            }
         }
         $first = false;
      }
      // convert string to array : a, b, c --> [{a}, {b}, {c}]
      for($i=0; $i<$nbOfColumns; $i++) {
         $res[$i] = explode(",", $res[$i]);
      }
   }
   // return an array of arrays ($res[0] is an array containing timestamps
   //                            $res[1] is an array containing temp of sensor 1)
   return $res;
}

function generateXYForOneDay($day, $month, $year) {
   $filename = $year."/".$month."/".$day;
   if (!file_exists($filename)) {
      print("<p>No file for $day.$month.$year</p>");
      return null;
   }
   $handle = fopen($filename, "r");
   if ($handle) {
      //print("<p>Graphs for $day/$month/$year</p>");
      $res = readAllRecords($handle); 
      fclose($handle);
      return $res;
   } else {
      print("<p>Cannot open file $filename</p>");
      return;
   }
}

function generateXYForAllLog($filename) {
   if (!file_exists($filename)) {
      print("<p>No file</p>");
      return null;
   }
   $handle = fopen($filename, "r");
   if ($handle) {
      //print("<p>Graphs for $day/$month/$year</p>");
      $res = readAllRecords($handle); 
      fclose($handle);
      return $res;
   } else {
      print("<p>Cannot open file $filename</p>");
      return;
   }
}

