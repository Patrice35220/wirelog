<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<?php
   $day = date("d");
   $month = date("m");
   $year = date("y");
   $graphTitle="Wirelog ".$day."/".$month."/".$year;
   //print("WireLog $day/$month/$year");
?>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      var view;
      var options;
      var maximumValues=new Array();
      function drawVisualization() {
         // Create and populate the data table.
         var data = new google.visualization.DataTable();
<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   $lines = generateXYForOneDay($day, $month, $year);
   print("         data.addColumn('datetime', 'time');\n");
   $nbOfLines=sizeof($lines);
   $nbOfMeasurements=sizeof($lines[0]);
   for($i=1; $i<$nbOfLines; $i++) {
      $maxSensor[$i] = -272;
   }
   for($i=1; $i<$nbOfLines; $i++) {
      print("         data.addColumn('number', '$sensors[$i]');\n");
      for($j=0; $j<$nbOfMeasurements; $j++) {
         $value = $lines[$i][$j];
         // Calculate max value per sensor
         if ($value > $maxSensor[$i]) {
            $maxSensor[$i] = $value;
         }
      }
   }
   $generalMax=-272;
      print("         maximumValues.push(0);\n"); // No sensor 0
   for($i=1; $i<$nbOfLines; $i++) {
      print("         maximumValues.push($maxSensor[$i]);\n");
      if ($maxSensor[$i] > $generalMax) {
         $generalMax = $maxSensor[$i];
      }
   }
   //print("General max is $generalMax");
   for($i=0; $i<$nbOfMeasurements; $i++) {
      $value = $lines[0][$i];
      print("         data.addRow([new Date($value)");
      for($j=1; $j<sizeof($lines); $j++) {
         $value = $lines[$j][$i];
         print(", $value");
      }
      print("]);\n");
   }
?>
         // Create and draw the visualization.
         options = { curveType: "function", interpolateNulls: true, 
<?php
   print("                         title: '$graphTitle',\n");
   print("                         legend: 'none',\n");
   print("                         vAxis: {maxValue: $generalMax, title:'Temperatures', gridlines:{count:10}}\n");
   $first = true;
   print("                         , colors:[");
   for($i=1; $i<$nbOfLines; $i++) {
      if ($first) {
         print("'$colors[$i]'");
         $first=false;
      } else {
         print(",'$colors[$i]'");
      } 
   }
   print("]\n");

   print("          };\n");
?>
         // Create a view (to be able to hide / show measurement)
         view = new google.visualization.DataView(data);

         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);
      }

      function clickOnSensor() {
<?php
      $listOfColumns="[0";
      for($i=1; $i<$nbOfLines; $i++) {
         $listOfColumns = $listOfColumns.",".$i;
      }
      $listOfColumns = $listOfColumns."]";
      print("         var updatedColors = new Array();\n");
      print("         var maxTemperature=-272;\n");
      print("         view.setColumns($listOfColumns);\n"); 
      for($i=1; $i<$nbOfLines; $i++) {
         print("         if (document.getElementById('buttonSensor$i').checked == 0) {\n"); 
         print("            view.hideColumns([$i]);\n");
         print("         } else {\n"); 
         print("            // Push back color in colorOption \n");
         print("            updatedColors.push('$colors[$i]');\n");
         print("            if (maximumValues[$i] > maxTemperature) {\n");
         print("               maxTemperature = maximumValues[$i];\n");  
         print("            }\n");
         print("         }\n"); 
      }
      print("         options.colors=updatedColors;\n"); 
      print("         options.vAxis.maxValue=maxTemperature;\n"); 
      print("         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);\n");
?>
      }

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
   <div id="container" style="width:1200px; height.800px; position:relative;">
      <div id="visualization" style="width: 1000px; height:800px; float:left;"></div>
      <div id="menu" style="width:200px;height:800px;position:relative;float:right;">
<?php
   for($i=1; $i<$nbOfLines; $i++) {
      print("         <p style=\"color:$colors[$i]\"><input id=buttonSensor$i type='checkbox' checked='checked' onclick='clickOnSensor()' />$sensors[$i]</p>\n");
   }
?>
      </div>
   </div>

  </body>
</html>
