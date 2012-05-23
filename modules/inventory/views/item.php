<?php
gatekeeper(3);
$currentUser = User::find_by_uid($_SESSION['cUser']['uid']);

if (isset($_POST['update_item'])) {
	// update item in inventory
	$updateItem = new Inventory();
	$updateItem->uid = $_POST['uid'];
	$updateItem->classroom_uid = $_POST['classroom_uid'];
	$updateItem->type = $_POST['type'];
	$updateItem->manufacturer = $_POST['manufacturer'];
	$updateItem->model = $_POST['model'];
	$updateItem->serial = $_POST['serial'];
	$updateItem->notes = $_POST['notes'];
	
	$updateItem->update();
}

$item = Inventory::find_by_uid($currentUser->school_uid, $_GET['itemUID']);
$room = Classroom::find_by_uid($item->classroom_uid);
$allRooms = Classroom::find_by_sql("SELECT * FROM classrooms WHERE school_uid = {$item->school_uid} ORDER BY name ASC");
$inventoryTypes = Inventory::find_types();
?>

<!-- main -->
<div id="main">
	<h2><?php echo ($item->manufacturer . " " . $item->model . " " . $item->type); ?></h2>
	
	<form target="_self" method="POST" name="edit_item" id="edit_item">
	<p>Purchase Date: <?php echo dateDisplay(strtotime($item->purchase_date), TRUE); ?>
	</p>
	<p>Serial Number: <br />
	<input type="text" name="serial" value="<?php echo ($item->serial); ?>" />
	</p>
	<p>Location: <br />
	<select name="room">
	<?php foreach($allRooms AS $uniqueRoom) {
		echo optionDropdown($uniqueRoom->uid, $uniqueRoom->name, $item->classroom_uid);
	}
	?>
	</select>
	</p>
	<p>Type: <br />
	<select name="type">
	<?php foreach($inventoryTypes AS $inventoryType) {
		echo optionDropdown($inventoryType->type, $inventoryType->type, $item->type);
	}
	?>
	</select>
	</p>
	<p>Manufacturer: <br />
	<input type="text" name="manufacturer" value="<?php echo ($item->manufacturer); ?>" />
	</p>
	<p>Model: <br />
	<input type="text" name="model" value="<?php echo ($item->model); ?>" />
	</p>
	<p>Notes: <br />
	<textarea name="notes" cols="80" rows="7"><?php echo ($item->notes); ?></textarea>
	</p>
	<input type="submit" value = "Update" />
	<input type="hidden" name="update_item" />
	<input type="hidden" name="uid" value="<?php echo ($item->uid); ?>" />
	</form>
</div>