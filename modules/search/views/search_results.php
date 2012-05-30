<?php
gatekeeper(3);

if (isset($_GET['quickSearchTerm']) && $_GET['quickSearchTerm'] <> "") {
	$searchString = $_GET['quickSearchTerm'];
} else {
	$searchString = $_POST['quickSearchTerm'];
	$_GET['status'] = "active";
}

if (isTechnician()) {
	$results = new Search();
	$jobResults = $results->findJobsBySearch($_POST['quickSearchTerm']);
	$notesResults = $results->find_all_notes($_POST['quickSearchTerm']);
	$inventoryResults = Inventory::findByString($_POST['quickSearchTerm']);
	
	foreach ($jobResults AS $result) {
		if ($result->type == "Job") {
			$job = Support::find_by_uid($result->uid);
			if ($job->active == "1") {
				$activeJobsOutput .= $job->displayJob();
				$activeJobsCount = $activeJobsCount + 1;
			} else {
				$closedJobsOutput .= $job->displayJob();
				$closedJobsCount = $closedJobsCount + 1;
			}
		} elseif ($result->type == "Response") {
			$job = Support::find_by_uid($result->spawn);
			if ($job->active == "1") {
				$activeJobsOutput .= $job->displayJob();
				$activeJobsCount = $activeJobsCount + 1;
			} else {
				$closedJobsOutput .= $job->displayJob();
				$closedJobsCount = $closedJobsCount + 1;
			}
		}
	}
	
}
?>

<div class="row-fluid">
<div class="span12">
	<div class="page-header">
		<h1>Search for '<?php echo $searchString; ?>' <small><?php echo count($jobResults) . autoPluralise(" job result"," job results",count($jobResults)); ?></small></h1>
	</div>
	
	<?php
	$rootAddress = "http://" . $_SERVER['HTTP_HOST'] . SITE_PATH;
	$activeJobsURL = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=active";
	$closedJobsURL = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=active";
	$inventoryResultsURL = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=active";
	$notesURL = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=active";
	?>
	<ul class="nav nav-tabs" id="myTab">
		<li class="active"><a href="#activeJobs" data-toggle="tab" data-parameter="<?php echo $activeJobsURL;?>"><span class="badge badge-info"><?php echo $activeJobsCount;?></span> Active Jobs</a></li>
		<li><a href="#closedJobs" data-toggle="tab" data-parameter="<?php echo $closedJobs;?>"><span class="badge badge-info"><?php echo $closedJobsCount;?></span> Closed Jobs</a></li>
		<li><a href="#inventoryResults" data-toggle="tab" data-parameter="<?php echo $inventoryResults;?>"><span class="badge badge-info"><?php echo count($inventoryResults);?></span> Inventory Results</a></li>
		<li><a href="#notes" data-toggle="tab" data-parameter="<?php echo $notes;?>"><span class="badge badge-info"><?php echo count($notesResults);?></span> Notes</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane active" id="activeJobs">
		<?php echo $activeJobsOutput; ?>
		</div>
		
		<div class="tab-pane" id="closedJobs">
		<?php echo $closedJobsOutput; ?>
		</div>
		
		<div class="tab-pane" id="inventoryResults">
			<?php
			if (count($inventoryResults) > 0) {
				
				echo ("<table class=\"table table-bordered table-striped\">");
				echo ("<thead><tr>");
				echo "<th style=\"width: 20%\">" . "Manufacturer" . "</th>";
				echo "<th style=\"width: 20%\">" . "Model" . "</th>";
				echo "<th style=\"width: 25%\">" . "Serial" . "</th>";
				echo "<th style=\"width: 25%\">" . "Purchase Date" . "</th>";
				echo "<th style=\"width: 10%\">" . "Status" . "</th>";
				echo ("</tr></thead>");
				
				foreach ($inventoryResults AS $item) {
					$uniqueItem  = "<tr>";
					$uniqueItem .= "<td>" . $item->manufacturer . "</td>";
					$uniqueItem .= "<td>" . $item->model . "</td>";
					$uniqueItem .= "<td>" . "<a href=\"node.php?m=inventory/views/item.php&amp;itemUID=" . $item->uid . "\">" . $item->serial . "</a></td>";
					$uniqueItem .= "<td>" . dateDisplay(strtotime($item->purchase_date), true) . "</td>";
					
					$checkbox = "<input type=\"checkbox\" class=\"inline\" name=\"touchUID\" value=\"" . $item->uid . "\">";
			
				    if (strtotime($item->last_modified) >= strtotime("-1 year")) {
				        $uniqueItem .= "<td>" . "<span class=\"label label-success\">Up-to-date " . $checkbox . "</span>" . "</td>";
				    } elseif (strtotime($item->last_modified) >= strtotime("-3 years")) {
				        $uniqueItem .= "<td>" . "<span class=\"label label-warning\">Needs Updating " . $checkbox . "</span>" . "</td>";
				    } else {
				        $uniqueItem .= "<td>" . "<span class=\"label label-important\">Missing " . $checkbox . "</span>" . "</td>";
				    }
				    $uniqueItem .= "</tr>";
	
					echo $uniqueItem;
				}
				
				echo ("</table>");
			}
			?>
		</div>
		
		<div class="tab-pane" id="notes">
		<?php
		if (count($notesResults) > 0) {
			// itterate through the notesResults
			foreach ($notesResults as $note) {
				echo $note->displayNote();
			}
		}
		?>
		</div>
	</div>
</div>
</div>