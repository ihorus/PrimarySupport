<?php
gatekeeper(2);

if (isset($_POST['edit_news_submit'])) {
	$updateNews = new News();
	$updateNews->uid = $_GET['newsUID'];
	$updateNews->description = $_POST['description'];
	$updateNews->title = $_POST['title'];
	$updateNews->active =  $_POST['active'];
	$updateNews->date =  $_POST['date'];
	$updateNews->update();
}

$newsItem = News::find_by_uid($_GET['newsUID']);
?>


<!-- main -->
<div id="main">
	<h2>Edit News Item <?php echo ($newsItem->uid); ?></h2>
	
	<form target="_self" method="POST" name="edit_news" id="edit_news">
	<p>Date: <br />
	<input type="text" name = "date" value = "<?php echo date('Y/m/d', strtotime($newsItem->date)); ?>"/>
	</p>
	<p>Title: <br />
	<input type="text" name="title" value = "<?php echo ($newsItem->title); ?>" />
	</p>
	<p>Description: <br />
	<textarea name="description" cols="80" rows="7"><?php echo ($newsItem->description); ?></textarea>
	</p>
	<p>Active: <br />
	<input type="checkbox" value = "1" name="active" <?php if ($newsItem->active == TRUE) { echo ("checked"); } ?> />
	</p>
	<input type="submit" value="Update" />
	<input type="hidden" name="edit_news_submit" />	
	</form>
</div>