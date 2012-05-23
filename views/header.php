<?php
if ($_SESSION['cUser']['username'] == "WILLIAMSC") {
	$class = "hero-unit2";
}elseif($_SESSION['cUser']['username'] == "RUSHTONP") {
	$class = "hero-unit3";
} else {
	$class = "hero-unit";
}
?>
<div class="<?php echo $class; ?>">
	<h1><?php echo(SITE_NAME); ?></h1>
	<p class="visible-desktop"><?php echo(SITE_SLOGAN); ?></p>
	<p class="call-to-action visible-desktop">
		<a href="node.php?m=support/views/index.php" class="btn btn-success btn-large">Computer Problems?</a> then <strong> log a job</strong></a>
		<br />or <a href="#">contact ICT Facilities</a> directly
	</p>
</div>