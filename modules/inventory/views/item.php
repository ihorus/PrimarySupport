<?php
gatekeeper(3);
$currentUser = User::find_by_uid($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']);

if (isset($_POST['update_item'])) {
	// update item in inventory
	$updateItem = new Inventory();
	$updateItem->uid = $_POST['uid'];
	$updateItem->classroom_uid = $_POST['classroom_uid'];
	$updateItem->type = $_POST['type'];
	$updateItem->manufacturer = $_POST['manufacturer'];
	$updateItem->model = $_POST['model'];
	$updateItem->notes = $_POST['notes'];
	$updateItem->value = $_POST['value'];
	
	$updateItem->update();
}

$item = Inventory::find_by_uid($currentUser->school_uid, $_GET['itemUID']);
$room = Classroom::find_by_uid($item->classroom_uid);
$allRooms = Classroom::find_by_sql("SELECT * FROM classrooms WHERE school_uid = {$item->school_uid} ORDER BY official_name ASC");
$inventoryTypes = Inventory::find_types();
?>

<script type="text/javascript">
$(function() {
	$("#touchUniqueItem").click(function() {
		// validate and process form here
				
		// description
		var itemUID = $("input#uid").val();
		
		// url we're going to send the data to
		var url = "modules/inventory/actions/touchItem.php";
		
		$.post(url,{
			itemUID: itemUID
		}, function(data){
			//$(this).css("btn-success");
			//$("#touchUniqueItem").css({"background-color": "#FFBFBF"});
			$("#touchUniqueItem").removeClass("btn-info");
			$("#touchUniqueItem").addClass("btn-success disabled");
			$('#touchUniqueItem').html('<i class="icon-hand-up icon-white"></i> Touched');
		},'html');
		
		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;

	});
	
});
</script>

<div class="row">
<div class="span12">
	<ul class="breadcrumb">
		<li><a href="node.php?m=inventory/views/index.php">Inventory</a> <span class="divider">/</span></li>
		<li><a href="node.php?m=inventory/views/room.php&roomUID=<?php echo $room->uid; ?>"><?php echo $room->roomName(); ?></a> <span class="divider">/</span></li>
		<li class="active"><?php echo $item->type; ?></li>
	</ul>
	
	<div class="page-header">
		<h1><?php echo $item->serial; ?> <small> <?php echo $room->roomName() . ": " . $item->manufacturer . " " . $item->model; ?></small></h1>
	</div>
	<form class="form-horizontal" target="_self" method="POST" name="edit_item" id="edit_item">
	<fieldset>
    <legend>Edit Inventory Item</legend>
    <div class="control-group">
    	<label class="control-label" for="input01">Purchase Date</label>
    	<div class="controls">
    		<input class="input-xlarge disabled" id="purchase_date" type="text" placeholder="<?php echo dateDisplay(strtotime($item->purchase_date), FALSE); ?>" disabled>
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="serial">Serial Number</label>
    	<div class="controls">
    		<input class="input-xlarge disabled" name="serial" type="text" placeholder="<?php echo ($item->serial); ?>" disabled>
    		<!-- <p class="help-block">If there are 2 serial numbers, please enter them both here</p> -->
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="classroom_uid">Location</label>
    	<div class="controls">
    		<select name="classroom_uid">
    			<?php foreach($allRooms AS $uniqueRoom) {
	    			echo optionDropdown($uniqueRoom->uid, $uniqueRoom->roomName(), $item->classroom_uid);
	    		}
	    		?>
	    	</select>
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="type">Type</label>
    	<div class="controls">
    		<select name="type">
	    		<?php foreach($inventoryTypes AS $inventoryType) {
		    		echo optionDropdown($inventoryType->type, $inventoryType->type, $item->type);
		    	}
		    	?>
		    </select>
    		<p class="help-block">Supporting help text</p>
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="manufacturer">Manufacturer</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" name="manufacturer" value="<?php echo ($item->manufacturer); ?>">
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="model">Model</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" name="model" value="<?php echo ($item->model); ?>">
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="value">Value</label>
    	<div class="controls">
    		<div class="input-prepend">
                <span class="add-on"><?php echo CURRENCY_SIGN; ?></span><input class="span2" name="value" size="16" type="text" value="<?php echo ($item->value); ?>">
            </div>
    	</div>
    </div>            
    <div class="control-group">
    	<label class="control-label" for="notes">Notes</label>
    	<div class="controls">
	    	<textarea class="input-xlarge" name="notes" rows="3"><?php echo ($item->notes); ?></textarea>
	    	
    	</div>
    </div>
    <div class="form-actions">
       <button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Save changes</button>
       <a class="btn btn-info" id="touchUniqueItem" href=""><i class="icon-hand-up icon-white"></i> Touch This Item</a>
	    	<input type="hidden" name="update_item" />
	    	<input type="hidden" id="uid" name="uid" value="<?php echo ($item->uid); ?>" />
    </div>
    </fieldset>
    </form>
</div>