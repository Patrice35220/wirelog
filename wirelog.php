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
         options = { curveType: "function", interpolateNulls: true, 
<?php
   print("                         title: '$graphTitle',\n");
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
         //view.hideColumns([3,4]);

         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);
      }

      function clickOnSensor() {
<?php
      $listOfColumns="[0";
      for($i=1; $i<$nbOfLines; $i++) {
         $listOfColumns = $listOfColumns.",".$i;
      }
      $listOfColumns = $listOfColumns."]";
      print("         view.setColumns($listOfColumns);\n"); 
      for($i=1; $i<$nbOfLines; $i++) {
         print("         if (document.getElementById('buttonSensor$i').checked == 0) {\n"); 
         print("            view.hideColumns([$i]);\n");
         print("         } else {\n"); 
         print("            // Push back color in colorOption \n");
         print("         }\n"); 
      }
      print("         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);\n");
?>
      }

<?php
   //for($i=1; $i<$nbOfLines; $i++) {
   //   print("      function clickOnSensor$i() {\n");
   //   print("         if (document.getElementById('buttonSensor$i').checked == 1) {\n"); 
   //   print("            view.showColumns([$i]);\n");
   //   print("         } else {\n");
   //   print("            view.hideColumns([$i]);\n");
   //   print("         }\n"); 
   //   print("         var linechart = new google.visualization.LineChart(document.getElementById('visualization')).draw(view, options);\n");
   //   print("      }");
   //}
?>

      google.setOnLoadCallback(drawVisualization);
    </script>
  </head>
  <body style="font-family: Arial;border: 0 none;">
   <div id="main" style="width:1000px; height.800px; position:relative;">
      <div id="menu" style="width:200px;height:800px;float:left;">
<?php
   for($i=1; $i<$nbOfLines; $i++) {
      print("         <p><input id=buttonSensor$i type='checkbox' checked='checked' onclick='clickOnSensor()' />$sensors[$i]</p>\n");
   }
?>
      </div>
      <div id="visualization" style="width: 800px; height:600px; position:relative;float:right;"></div>
   </div>

  </body>
</html>
