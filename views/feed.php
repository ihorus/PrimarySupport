<?php
require_once("../engine/initialise.php");

if (isTechnician()) {
	// generate a list of all jobs (although limit the list to 25)
	$activeJobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND active = TRUE ORDER BY entry LIMIT 25");
} else {
	// generate a list of all jobs for a school (limit to 25)
	$activeJobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND active = TRUE AND school_uid = " . $_SESSION[SITE_UNIQUE_KEY]['cUser']['schoolUID'] . " ORDER BY entry LIMIT 25");
}

//$query_jobs_active
?>
<rss xmlns:feed="urn:feed" version="2.0">

<channel>
	<title><?php echo (SITE_NAME . " - " . SITE_SLOGAN); ?></title>
	<description>Active support calls logged on the Primary Support helpdesk.</description>
	<link>http://<?php echo ($_SERVER['SERVER_NAME']); ?></link>
	<copyright>Wallingford School, ICT Facilities</copyright>
	<webmaster>ab2394@wallingford.oxon.sch.uk (Andrew Breakspear)</webmaster>
	<language>en-gb</language>
	<pubDate><?php echo date('r'); ?></pubDate>
	<lastBuildDate><?php echo date('r'); ?></lastBuildDate>
	<ttl>30</ttl>

	<?php if (count($activeJobs>0)) {
		foreach ($activeJobs AS $job) {
			$school = Group::find_by_uid($job->school_uid);
			$entryUser = User::find_by_uid($job->entry_uid);
			
			$rss_item  = "<item>";
			$rss_item .= "<title>" . $school->name . ": " . $job->uid . "</title>";
			$rss_item .="<author>" . $user->first_name . "</author>";
			$rss_item .="<link>http://" . $_SERVER['SERVER_NAME'] . "/node.php?m=support/views/support_unique.php&amp;supportUID=" . $job->uid . "</link>";
			$rss_item .="<category>Support Request</category>";
			$rss_item .="<description>" . str_replace("&","and",$job->description) . "</description>";
			$rss_item .="<pubDate>" . $job->entry . "</pubDate>";
			$rss_item .="</item>";
			
			echo $rss_item;
		}
	} // Show if recordset not empty
?>
</channel>
</rss>