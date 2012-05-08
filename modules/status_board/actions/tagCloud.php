<?php
require_once("../../../engine/initialise.php");

function printTagCloud($tags) {
        // $tags is the array
       
        arsort($tags);
       
        $max_size = 32; // max font size in pixels
        $min_size = 12; // min font size in pixels
       
        // largest and smallest array values
        $max_qty = max(array_values($tags));
        $min_qty = min(array_values($tags));
       
        // find the range of values
        $spread = $max_qty - $min_qty;
        if ($spread == 0) { // we don't want to divide by zero
                $spread = 1;
        }
       
        // set the font-size increment
        $step = ($max_size - $min_size) / ($spread);
       
        // loop through the tag array
        foreach ($tags as $key => $value) {
                // calculate font-size
                // find the $value in excess of $min_qty
                // multiply by the font-size increment ($size)
                // and add the $min_size set above
                $size = round($min_size + (($value - $min_qty) * $step));
       
                echo '<a href="#" style="font-size: ' . $size . 'px" 
title="' . $value . ' things tagged with ' . $key . '">' . $key . '</a> ';
        }
}



$allJobs = Support::find_all();

foreach ($allJobs AS $job) {
	//echo $job->description;
	$array = explode(" ", $job->description);
		
	foreach ($array AS $arrayElement) {
			$tags[] = $arrayElement;
	}
	
}


function ArrayGroupByCount($_array, $sort = false) {
   $count_array = array();

   foreach (array_unique($_array) as $value) {
       $count = 0;

		foreach ($_array as $element) {
		    if ($element == $value)
		        $count++;
		}
	
		$count_array[$value] = $count;
	}
	
	if ( $sort == 'desc' )
		arsort($count_array);
	elseif ( $sort == 'asc' )
		asort($count_array);

	return $count_array;
}


//printArray(ArrayGroupByCount($tags));





//$tags = array('weddings' => 32, 'birthdays' => 41, 'landscapes' => 62, 'ham' => 51, 'chicken' => 23, 'food' => 91, 'turkey' => 47, 'windows' => 82, 'apple' => 27);

printTagCloud($tags);




?>