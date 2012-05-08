<!-- left-columns starts -->
<div id="sidemenu_content">
	<h3>Supported Schools</h3>
	<ul>
		<?php
		// find all active schools (i.e. the ones we support)
		$schools = Group::find_all();
		
		foreach ($schools as $school) {			
			echo ("<li><a href=\"node.php?n=school_overview&amp;schoolUID=" . $school->uid . "&filter=active\">" . $school->schoolname() . "</a>");
			if ($school->totalActiveJobs() >=1) {
				$li  = ("<br />");
				$li .= ("<a href=\"node.php?n=school_overview&amp;schoolUID=" . $school->uid . "&amp;filter=active\">");
				$li .= ("<span>");
				$li .= ($school->totalActiveJobs() . " active " . autoPluralise("job", "jobs", $school->totalActiveJobs()));
				$li .= ("</span></a>");
				
				echo $li;
			}
			echo ("</li>");
		}
		?>
	</ul>
</div>