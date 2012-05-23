<?php
gatekeeper(3);

$currentUser = User::find_by_uid($_SESSION['cUser']['uid']);

if (isset($_POST['add_item'])) {
	// add item to the inventory
	$item = new Inventory();
	
	$item->school_uid = $_POST['school_uid'];
	$item->classroom_uid = $_POST['classroom_uid'];
	if ($_POST['typeOther'] == "") {
		$item->type = $_POST['type'];
	} else {
		$item->type = $_POST['typeOther'];
	}
	$item->manufacturer = $_POST['manufacturer'];
	$item->model = $_POST['model'];
	$item->serial = $_POST['serial'];
	$item->notes = $_POST['notes'];
	$item->purchase_date = $_POST['purchase_date'];
	
	// insert item to the database
	$item->create();
}


$classroom = Classroom::find_by_uid($_GET['roomUID']);
$classrooms = Classroom::find_all_by_school($classroom->school_uid);

$types = Inventory::find_by_sql("SELECT * FROM inventory WHERE school_uid = {$currentUser->school_uid} AND classroom_uid = {$classroom->uid} GROUP BY type");

$allTypes = Inventory::find_types();

?>
<script>

$(function() {
	$('#typeOther').hide();
	
	$("#type").change(function() {
		if ($('#type').val() == 'Other') {
			$('#typeOther').show();
		} else  {
			$('#typeOther').hide();
			$('#typeOther').val('');
		}
	});
});
</script>
<!-- main -->
<div id="main">
	<h2><?php echo $classroom->name; ?></h2>
	<?php
	
	foreach ($types AS $type) {
		$items = Inventory::find_all_by_room($currentUser->school_uid, $classroom->uid, $type->type);
	
		echo ("<h3> (x" . count($items) . ") " . $type->type . "</h3>");
		
		echo ("<ul>");
		foreach ($items AS $item) {
			$uniqueItem  = "<li>";
			$uniqueItem .= ("<a href=\"node.php?m=inventory/views/item.php&amp;itemUID=");
			$uniqueItem .= ($item->uid . "\">");
			$uniqueItem .= $item->serial . " - " . $item->manufacturer . " " . $item->model;
			$uniqueItem .= ("</a>");
			$uniqueItem .= "</li>";
	
			echo $uniqueItem;
		}
	}
?>

	<form target="_self" method="POST" name="add_job" id="add_job">
	<h2>Add New Item</h2>
	<p>Classroom: <br />
	<select name = "classroom_uid">
		<?php
			foreach ($classrooms AS $uniqueClassroom) {
				echo optionDropdown($uniqueClassroom->uid, $uniqueClassroom->name, $classroom->uid) ;
			}
		?>
	</select>
	</p>
	<p>Type: <br />
	<select name="type" id="type">
		<?php
			foreach ($allTypes AS $type) {
				echo optionDropdown($type->type, $type->type, "0") ;
			}
			echo optionDropdown("Other", "Other", 99) ;
		?>
	</select>
		<input type="text" name="typeOther" id="typeOther" />
		</p>
	<p>Manufacturer: <br />
	<input type="text" name = "manufactuer" />
	</p>
	<p>Model: <br />
	<input type="text" name = "model" />
	</p>
	<p>Serial: <br />
	<input type="text" name = "serial" />
	</p>
	<p>Purchase Date: <br />
	<input type="text" name = "purchase_date" value = "<?php echo (date('Y/m/d'));?>" />
	</p>
	<p>Notes: <br />
	<textarea name = "notes" cols="80" rows="7"></textarea>
	</p>
	<input type="submit" />
	<input type="hidden" name = "school_uid" value = "<?php echo ($classroom->school_uid); ?>" />
	<input type="hidden" name = "add_item" />
	</form>
</div>