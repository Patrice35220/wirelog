<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   // Debug 
   // $fd = fopen("testLog", "w");
   // fputs($fd, "datasource req Received\n");
   // foreach($_GET as $key => $i){
   //   fputs($fd, "$key=$_GET[$key]\n");
   // }
   // fclose($fd);

   // Parse request
   $tqx = $_GET["tqx"];
   $tokens = explode(",", $tqx);
   for($i=0; $i<sizeof($tokens); $i++) {
      $pos=strpos($tokens[$i], "reqId");
      if ($pos !== false) {
         $subTokens = explode(":", $tokens[$i]);
         if (sizeof($subTokens) > 1) {
            $reqId=$subTokens[1];
            break;
         }
      }
   }

   // Get sensors values for 'today'
   $day = date("d");
   $month = date("m");
   $year = date("y");
   $lines = generateXYForOneDay($day, $month, $year);

   // Encode response as JSON 
   $response = "{version:'0.6'";
   // set reqId in response if it is present in request
   if ($reqId != "") {
      $response = $response.",reqId:'".$reqId."'";
   }
   // Add columns
   $response = $response.",status:'ok',table:{cols:[{label:'time', type:'datetime'}";
   $nbOfLines=sizeof($lines);
   $nbOfMeasurements=sizeof($lines[0]);
   for($i=1; $i<$nbOfLines; $i++) {
      $response = $response.",{label:'$sensors[$i]', type:'number'}";
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
      for($j=1; $j<sizeof($lines); $j++) {
         $value = $lines[$j][$i];
         $response = $response.",{v:$value}";
      }
      $response = $response."]}";
   }
   $response = $response."]"; // end of rows
   $response = $response."}"; // end of table
   $response = $response."}"; // end of response
   echo $response;
?>
