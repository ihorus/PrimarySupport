<?php
gatekeeper(3);

$currentUser = User::find_by_uid($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']);

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
	$item->value = $_POST['value'];
	$item->notes = $_POST['notes'];
	$item->purchase_date = $_POST['purchase_date'];
	
	// insert item to the database
	if ($item->create()) {
		$addItemResult = TRUE;
	} else {
		$addItemResult = FALSE;
	}
}


$classroom = Classroom::find_by_uid($_GET['roomUID']);
$classrooms = Classroom::find_all_by_school($classroom->school_uid);

$types = Inventory::find_by_sql("SELECT * FROM inventory WHERE school_uid = {$currentUser->school_uid} AND classroom_uid = {$classroom->uid} GROUP BY type");

$allTypes = Inventory::find_types();

?>
<script type="text/javascript">
$(function() {
	$("#touchMultipleItems").click(function() {
		// validate and process form here
		var itemUID = new Array();
		
		// description
		$("input:checkbox[name=touchUID]:checked").each(function() {
			itemUID.push($(this).val());
		});
		
		// url we're going to send the data to
		var url = "modules/inventory/actions/touchItem.php";
		
		$.post(url,{
			itemUID: itemUID
		}, function(data){
			//$(this).css("btn-success");
			//$("#touchUniqueItem").css({"background-color": "#FFBFBF"});
			$("#touchMultipleItems").removeClass("btn-info");
			$("#touchMultipleItems").addClass("btn-success disabled");
			$('#touchMultipleItems').html('<i class="icon-hand-up icon-white"></i> Touched');
		},'html');
		
		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;

	});
	
});
</script>
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

<div class="row">
<div class="span12">
	<ul class="breadcrumb">
		<li><a href="node.php?m=inventory/views/index.php">Inventory</a> <span class="divider">/</span></li>
		<li class="active"><?php echo $classroom->roomName(); ?></li>
	</ul>
	<div class="page-header">
		<h1><?php echo $classroom->roomName(); ?> <small><?php echo count($classroom->contents()); ?> items</small>
		<small class="pull-right">
		<?php
		$value = new Inventory();
		$value->classroom_uid = $classroom->uid;
		$value = $value->value_by_room();
		echo moneyDisplay($value->value);
		?>
		</small></h1>
	</div>
	
	<?php
	if (isset($_POST['add_item']) && $addItemResult == TRUE) {
		echo "<div class=\"alert alert-success\"><strong>Success</strong> " . $_POST['type'] . " added to inventory</div>";
	} elseif (isset($_POST['add_item']) && $addItemResult == FALSE) {
		echo "<div class=\"alert\"><strong>Warning!</strong> Error adding item to inventory.</div>";
		printArray($_POST);
	}
	foreach ($types AS $type) {
		$items = Inventory::find_all_by_room($currentUser->school_uid, $classroom->uid, $type->type);
	
		echo ("<h3>" . count($items) . " " . $type->type . "</h3>");
		
		echo ("<table class=\"table table-bordered table-striped\">");
		echo ("<thead><tr>");
		echo "<th style=\"width: 20%\">" . "Manufacturer" . "</th>";
		echo "<th style=\"width: 20%\">" . "Model" . "</th>";
		echo "<th style=\"width: 25%\">" . "Serial" . "</th>";
		echo "<th style=\"width: 25%\">" . "Purchase Date" . "</th>";
		echo "<th style=\"width: 10%\">" . "Status" . "</th>";
		echo ("</tr></thead>");
		
		foreach ($items AS $item) {
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
	<a class="btn btn-info pull-right" id="touchMultipleItems" href=""><i class="icon-hand-up icon-white"></i> Touch Selected Items</a>
</div>
</div>

<div class="row">
<div class="span12">
	<form class="form-horizontal" target="_self" method="POST" name="add_job" id="add_job">
	<fieldset>
    <legend>Add New Item</legend>
    <div class="control-group">
    	<label class="control-label" for="classroom_uid">Classroom</label>
    	<div class="controls">
    		<select name="classroom_uid">
    			<?php foreach($classrooms AS $uniqueClassroom) {
	    			echo optionDropdown($uniqueClassroom->uid, $uniqueClassroom->roomName(), $classroom->uid);
	    		} ?>
	    	</select>
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="type">Type</label>
    	<div class="controls">
    		<select name="type">
    			<?php
	    		foreach ($allTypes AS $type) {
					echo optionDropdown($type->type, $type->type, "0") ;
				}
				echo optionDropdown("Other", "Other", "Other") ;
				?>
	    	</select>
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="manufacturer">Serial</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" name="serial">
    		<p class="help-block">If there are multiple serial numbers, enter them all here</p>
    	</div>
    </div>
    <div class="control-group">
    	<?php
	    $manufacturers = Inventory::find_by_sql("SELECT manufacturer FROM inventory GROUP BY manufacturer ORDER BY manufacturer DESC");
		foreach ($manufacturers AS $manufacturer) {
			$manOutput[] = "\"" . $manufacturer->manufacturer . "\"";
		}
		$manOutput = array_unique($manOutput);
		?>
    	<label class="control-label" for="manufacturer">Manufacturer</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" data-provide="typeahead" data-items="4" data-source='[<?php echo implode(",", $manOutput); ?>]' name="manufacturer">
    	</div>
    </div>
    <div class="control-group">
  		<?php
	    $models = Inventory::find_by_sql("SELECT model FROM inventory GROUP BY model ORDER BY model DESC");
		foreach ($models AS $model) {
			$modOutput[] = "\"" . str_replace("\"", "", $model->model) . "\"";
		}
		$modOutput = array_unique($modOutput);
		?>
    	<label class="control-label" for="model">Model</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" data-provide="typeahead" data-items="4" data-source='[<?php echo implode(",", $modOutput); ?>]' name="model">
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="value">Value</label>
    	<div class="controls">
    		<div class="input-prepend">
                <span class="add-on"><?php echo CURRENCY_SIGN; ?></span><input class="span2" name="value" size="16" type="text">
            </div>
    	</div>
    </div>            
    <div class="control-group">
    	<label class="control-label" for="notes">Notes</label>
    	<div class="controls">
	    	<textarea class="input-xlarge" name="notes" rows="3"></textarea>
	    	
    	</div>
    </div>
    <div class="control-group">
    	<label class="control-label" for="purchase_date">Purchase Date</label>
    	<div class="controls">
    		<input type="text" class="input-xlarge" name="purchase_date" value="<?php echo (date('Y-m-d'));?>">
    	</div>
    </div>	
	<div class="form-actions">
       <button type="submit" class="btn btn-primary">Add Item</button>
	    	<input type="hidden" name = "school_uid" value = "<?php echo ($classroom->school_uid); ?>" />
	    	<input type="hidden" name = "add_item" />
    </div>
	</fieldset>
	</form>
</div>
</div>