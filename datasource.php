<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   $reqId = $_GET["reqId"];
   $day = date("d");
   $month = date("m");
   $year = date("y");
   $lines = generateXYForOneDay($day, $month, $year);
   $response = "{version:'0.6'";
   // set reqId in response if it is present in request
   if ($reqId != "") {
      $response = $response.",reqId:'".$reqId."'";
   }
   // Add columns
   $response = $response.",status:'ok',table:{cols:[{id:'time', label:'', type:'datetime'}";
   //print("         data.addColumn('datetime', 'time');\n");
   $nbOfLines=sizeof($lines);
   $nbOfMeasurements=sizeof($lines[0]);
   for($i=1; $i<$nbOfLines; $i++) {
      $response = $response.",{id:'$sensors[$i]', label:'', type:'number'}";
      //print("         data.addColumn('number', '$sensors[$i]');\n");
   }
   // end columns array
   $response = $response."]";
   // Add rows
   $response = $response.",rows:[";
   for($i=0; $i<$nbOfMeasurements; $i++) {
      $value = $lines[0][$i];
      if ($i>0) {
         $response = $response.",";
      }
      $response = $response."{c:[{v:new Date($value)}"; // datetime
      //print("         data.addRow([new Date($value)");
      for($j=1; $j<sizeof($lines); $j++) {
         $value = $lines[$j][$i];
         $response = $response.",{$value}";
         //print(", $value");
      }
      $response = $response."]}";
      //print("]);\n");
   }
   $response = $response."]"; // end of rows
   $response = $response."}"; // end of table
   $response = $response."}"; // end of response
   echo $response;
?>
