<?php
gatekeeper(3);
?>

<!-- main -->
<div class="row">
<div class="span12">
	<div class=\"page-header\">
		<h1>Recent News <small> what's happening</small></h1>
	</div>
	
	<?php	
	$news = News::find_subset($limit = 5, $active = TRUE);
	

	foreach ($news as $newsItem) {
		echo $newsItem->displayNewsItem();
	}
	?>
</div>
</div>
<!-- main ends -->