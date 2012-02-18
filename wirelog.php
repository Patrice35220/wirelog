<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>
<?php
   $day = date("d");
   $month = date("m");
   $year = date("y");
   print("WireLog $day/$month/$year");
?>
    </title>
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script type="text/javascript">
      function drawVisualization() {
         // Create and populate the data table.
         var data = new google.visualization.DataTable();
<?php
   include_once("logFileParser.inc");
   include_once("settings.inc");

   $colors = array("black", "grey", "red", "orange", "BlueViolet", "green", "blue");
   $lines = generateXYForOneDay($day, $month, $year);
   print("         data.addColumn('datetime', 'time');\n");
   $nbOfLines=sizeof($lines);
   $nbOfMeasurements=sizeof($lines[0]);
   $generalMax=0;
   for($i=1; $i<$nbOfLines; $i++) {
      print("         data.addColumn('number', '$sensors[$i]');\n");
      for($j=0; $j<$nbOfMeasurements; $j++) {
         $value = $lines[$i][$j];
         if ($value > $generalMax) {
            $generalMax = $value;
         }
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
         var options = { curveType: "function", width: 1000, height: 800, interpolateNulls: true, 
<?php
   print("                         vAxis: {maxValue: $generalMax}};\n");
?>
         // Create a view (to be able to hide / show measurement)
         var view = new google.visualization.DataView(data);
         //view.hideColumns([3,4]);

         new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);
      }

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
<?php
   $day = date("d");
   $month = date("m");
   $year = date("y");
   print("<H><center>WireLog $day/$month/$year</center></H>");
?>
    <div id="visualization" style="width: 1000px; height: 800px;"></div>
  </body>
</html>
