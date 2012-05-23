<?php
gatekeeper(3);

if (isset($_GET['quickSearchTerm']) && $_GET['quickSearchTerm'] <> "") {
	$searchString = $_GET['quickSearchTerm'];
} else {
	$searchString = $_POST['quickSearchTerm'];
	$_GET['status'] = "active";
}

$results = new Search();
$results->searchString = $searchString;

if (isTechnician()) {
	// current user is a technician or admin, search all jobs
	$jobResults = $results->find_all_jobs($_GET['status']);
	$notesResults = $results->find_all_notes();
} else {
	// current user is a user, only search their own school's jobs
	$jobResults = $results->find_school_jobs($_SESSION[SITE_UNIQUE_KEY]['cUser']['schoolUID']);

}
?>

<div class="span12">
	<div class="page-header">
		<h1>Search for '<?php echo $searchString; ?>' <small><?php echo count($jobResults) . autoPluralise(" result"," results",count($jobResults)); ?></small></h1>
	</div>
	
	<div class="btn-group">
		<?php
			$statusArray = array("active","closed","all");
			$baseURL = "node.php?m=search/views/search_results.php&quickSearchTerm=" . $searchString;
			
			foreach ($statusArray AS $status) {
				if ($status == $_GET['status']) {
					$output  = "<button class=\"btn btn-success btn-large\">";
				} else {
					$output  = "<button class=\"btn btn-large\">";
				}
				
				$output .= "<a href=\"" . $baseURL . "&status=" . $status . "\">";
				$output .= ucwords($status);
				$output .= "</a>";
				$output .= "</button>";
				
				echo $output;
			}
			
			
			?>
	</div>
<?php
if (count($notesResults) > 0) {
	// itterate through the notesResults
	foreach ($notesResults as $note) {
		$noteObject = Notes::find_by_uid($note->uid);
		
		echo $noteObject->displayNote();
	}
}
	
if (count($jobResults) > 0) {
	$limitedJobs = paginateResults($jobResults);
		
	//display pagination navigation
	//echo paginationNavBar(count($jobResults));
	
	// itterate through the search_results
	foreach ($limitedJobs as $search_result) {
		$job = Support::find_by_uid($search_result);
		
		if ($job->type == "Job") {
			// the search result is an original job, so just display it
			echo $job->displayJob();
		} else {
			// the search result is a response, so display the original job instead
			echo $job->displayJob();
		}
	}
	
	//display pagination navigation
	echo paginationNavBar(count($jobResults));
}
?>