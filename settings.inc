<?php
   function getSensorsLabels() {
      $sensors = array("none", "Collector Fluid", "Hot Water Output", "Water heating", "Heating", "Outside temperature", "Solar Storage Low", "temp1", "temp2", "temp3", "temp4", "temp5", "temp6", "temp7", "temp8", "temp9", "temp10", "temp11");
      return $sensors;
   }
   $colors = array("black", "floralwhite", "red", "chartreuse", "BlueViolet", "green", "coral", "gold", "grey", "yellowGreen", "purple", "salmon", "chartreuse");

   $sensorForExtTemp=5;

   $datasource = 'http://patrice.den.free.fr/wirelog/datasource.php';
?>

