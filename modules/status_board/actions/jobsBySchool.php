<?php
require_once("../../../engine/initialise.php");

$jobsBySchool = Group::find_all();


foreach ($jobsBySchool AS $uniqueJobsBySchool) {
	if (!$uniqueJobsBySchool->totalActiveJobs() == 0) {
		$schoolArray[$uniqueJobsBySchool->name] = $uniqueJobsBySchool->totalActiveJobs();
	}
}
$totalJobsBySchool = array_values($schoolArray);
$schools = array_keys($schoolArray);
krsort($schools);


//printArray($schools);
//printArray($totalJobsBySchool);



$googleURL = ("http://chart.apis.google.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World");

$baseURL = ("http://chart.apis.google.com/chart?");
$chartType = ("cht=bhs&chtt=Jobs By School");
$chartSize = ("&chs=500x295");
$chartMax = ("&chds=0," . (max($totalJobsBySchool)*1.1) . "&chxr=0,0," . (max($totalJobsBySchool)*1.1) . ",2");
$chartLables = ("&chxt=x,y&chxl=1:|" . implode("|", $schools) . "|");
$chartData = ("&chf=bg,s,65432100&chd=t:" . implode(",", $totalJobsBySchool));

$url = ($baseURL . $chartType . $chartSize . $chartMax . $chartData. $chartLables);

echo ("<img src=\"" . $url . "\" />");
?>



