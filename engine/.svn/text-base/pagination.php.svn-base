<?php

function paginateResults($arrayOfResults) {
	$fromTo = paginateFromTo();
	
	$fromTo = explode("/", $fromTo);
	
	$limitedJobs = array_slice($arrayOfResults, $fromTo[0], $fromTo[1]);
	
	return $limitedJobs;
}

function paginateCheckRequest() {
	// check for pagination
	if (isset($_GET['pagenum'])) {
		$request = TRUE;
	} else {
		$requst = FALSE;
	}
	
	return $request;
}


function paginateFromTo() {
	$resultsPerPage = 10;
	
	// check for pagination
	if (paginateCheckRequest()) {
		if ($_GET['pagenum'] == 1||$_GET['pagenum'] == 0) {
			$resultsFrom = 0;
			$resultsTo = $resultsPerPage;
			$_GET['pagenum'] = 1;
		} else {
			$resultsFrom = (($_GET['pagenum'] * $resultsPerPage)-$resultsPerPage);
			$resultsTo = $resultsPerPage;
		}
		
		// need to check we havn't gone over the maximum number of records
	} else {
		$resultsFrom = 0;
		$resultsTo = $resultsPerPage;
		$_GET['pagenum'] = 1;
	}
	
	return $resultsFrom . "/" . $resultsTo;
}

function paginationNavBar($totalPages = 0) {
	$resultsPerPage = 10;
	$totalPages = ceil($totalPages/$resultsPerPage);

	// fetch the current URL of the page
	$currentURL =  $_SERVER['REQUEST_URI'];
	
	// check if 'pagenum' is already set, as we don't want to repeat that variable
	// if it is present, remove it using preg)_replace
	$searchPatterns = array ('/&pagenum=[0-9]{0,5}/');
	$trimedURL = preg_replace($searchPatterns, "", $currentURL);
	
	$navBar  = "<div class=\"pagination pagination-centered\">";
	$navBar .= "<ul>";
	$navBar .= "<li><a href=\"#\">&larr; Previous</a></li>";
	
	$i = 1;
	do {
		if ($i == $_GET['pagenum']){
			$numBar = "<li class=\"active\">";
		} else {
			$numBar = "<li>";
		}
		$numBar .= "<a href=\"" . $trimedURL . "&pagenum=" . $i . "\">";
		$numBar .= $i;
		$numBar .= "</a>";
		$numBar .= "</li>";
		
		$navBar .= $numBar;
		
		$i = $i + 1;
	} while ($i <= 10);
	
	$navBar .= "<li><a href=\"#\">Next &rarr;</a></li>";
	$navBar .= "</ul>";
	$navBar .= "</div>";
	
	if ($totalPages > 1) {
		return $navBar;
	} else {
		return false;
	}
}
?>