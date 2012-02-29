<?php
include_once("utils.inc");

function detectNumberOfSensors($line) {
   $tokens = explode(";", $line);
   if (sizeof($tokens) > 1) {
      return (sizeof($tokens)-1);
   } else {
      return 0;
   }
}

// Return an array, first element is timestamp
function readTemperatureRecord($line) {
   $line = trim($line); // remove end of line
   $res = explode(";", $line);
   return $res; 
}

function reorganizeDataPerSensor($lines)
{
   $res = array();
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

function readAllRecordsForOneDay($day, $month, $year) {
   $filename = $year."/".$month."/".$day;
   // Results array
   $lines = array();

   if (!file_exists($filename)) {
      // No file -> no data
      print("<p>no file</p>\n");
      return ;
   }
   $handle = fopen($filename, "r");
   if ($handle) {
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
               //print("         //numbers = $numbers");
               //print("         //expectedNbOfRecords = $expectedNbOfRecords\n");
            }
         } else {
            $tt = sizeof($record);
            //print("         //sizeof(record) = $tt, expectedNbOfRecords=$expectedNbOfRecords\n");
         }
         $line = fgets($handle);
      }
      fclose($handle);
   }
   return $lines;
}

function generateXYForOneDay($day, $month, $year) {
   $lines = readAllRecordsForOneDay($day, $month, $year); 
   $res = reorganizeDataPerSensor($lines);
   return $res;
}

function generateXYForDates($fromDate, $toDate)
{
   list($fromDay, $fromMonth, $fromYear) = explode("/", $fromDate);
   list($toDay, $toMonth, $toYear) = explode("/", $toDate);
   $lines = array();
   $allLines = array();

   $lastDate = mktime(0,0,0, $toMonth, $toDay, $toYear);
   //print("last date is $lastDate\n");

   $currentDay = $fromDay;
   $currentMonth = $fromMonth;
   $currentYear = $fromYear;

   while ($currentDay != "" && $currentMonth != "" && $currentYear != "")
   {
      $lines = readAllRecordsForOneDay($currentDay, $currentMonth, $currentYear); 
      if (sizeof($lines) > 0) {
         // Merge lines to allLines
         for($i=0; $i<sizeof($lines); $i++) {
            array_push($allLines, $lines[$i]);
         }
      }
      $currentDate = mktime(0,0,0, $currentMonth, $currentDay, $currentYear);
      //print("current date = $currentDate\n");
      $currentDate = $currentDate + 86400; 
      //print("currentDate incremented= $currentDate\n");
      if ($currentDate > $lastDate) {
         break;
      } else {
         $strDate = date("d/m/y", $currentDate);
         list($currentDay, $currentMonth, $currentYear) = explode("/", $strDate);
         //print("looping with $currentDay-$currentMonth-$currentYear\n");
      }
   }
   $res = reorganizeDataPerSensor($allLines);
   return $res;
}

function generateXYForAllLog($filename) {
   if (!file_exists($filename)) {
      print("<p>No file</p>");
      return null;
   }
   $handle = fopen($filename, "r");
   if ($handle) {
      //print("<p>Graphs for $day/$month/$year</p>");
      $res = readAllRecordsFromFile($handle); 
      fclose($handle);
      return $res;
   } else {
      print("<p>Cannot open file $filename</p>");
      return;
   }
}

?>

