<?php
require_once("../../../engine/initialise.php");
?>

<?php
$m= date("m");
$de= date("d");
$y= date("Y");

for($i=0; $i<=90; $i++)	{
	$tempDate = date('Y-m-d',mktime(0,0,0,$m,($de-$i),$y)); 
	//echo $tempDate;
	
	$activityByDay = Activity::find_total_activity_by_day($tempDate);
	//echo " - " . count($activityByDay);
	//echo "<br>";*/
	$JobsByDayArray[] = count($activityByDay);
}


krsort($JobsByDayArray);



$baseURL = ("http://chart.apis.google.com/chart");
$chartType = ("?cht=lc&chtt=Activity By Day (Past 3 Months)");
$chartSize = ("&chf=bg,s,65432100&chs=295x295&chxt=y&chxr=0,0,0,0|0,0," . max($JobsByDayArray)*1.1 . ",10&chds=0," . max($JobsByDayArray)*1.1);
$chartData = ("&chd=t:" . implode(",",$JobsByDayArray));


$url = ($baseURL . $chartType . $chartSize . $chartData);


echo ("<img src=\"" . $url . "\" />");


?>