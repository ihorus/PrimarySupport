<?php
gatekeeper(2);

if (isset($_POST['add_news_submit'])) {
	$addNews = new News();
	$addNews->uid = $_GET['newsUID'];
	$addNews->description = $_POST['description'];
	$addNews->title = $_POST['title'];
	$addNews->active =  $_POST['active'];
	$addNews->date =  $_POST['date'];
	$addNews->user_uid = $_SESSION['cUser']['uid'];
	
	if ($addNews->create()) {
		$addNewsComplete = TRUE;
	}
}

?>


<!-- main -->
<div id="main">
	<?php if ($addNews == TRUE) {
		echo ("<h2>News Item Added Successfully</h2>");
	}
	?>
	<h2>Add New News Item</h2>
	
	<form target="_self" method="POST" name="add_news" id="edit_news">
	<p>Date: <br />
	<input type="text" name = "date" value = "<?php echo date('Y/m/d H:m'); ?>"/>
	</p>
	<p>Title: <br />
	<input type="text" name="title" value = "(e.g. Support Unavailable)" />
	</p>
	<p>Description: <br />
	<textarea name="description" cols="80" rows="7"></textarea>
	</p>
	<p>Active: <br />
	<input type="checkbox" value = "1" name="active" checked />
	</p>
	<input type="submit" value="add" />
	<input type="hidden" name="add_news_submit" />	
	</form>
</div>