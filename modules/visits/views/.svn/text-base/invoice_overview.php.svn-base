<?php
gatekeeper(1);

//get list of ALL groups (not just active)
$schools = Group::find_all("FALSE");

?>

<div class="grid_9 alpha omega">
	<form action="modules/visits/views/invoice_specific.php" method="POST" name="invoice" id="invoice">
	<h2>Generate Invoice</h2>
	<p>School: <br />
	<select name="schoolUID">
		<?php foreach($schools AS $school) {
		echo optionDropdown($school->uid, $school->name);
		} ?>
	</select>
	</p>
	<p>Invoice From: <br />
	<input type="text" name = "invoiceFrom" value = "<?php echo date('Y/m/d'); ?>"/>
	</p>
	<p>Invoice To: <br />
	<input type="text" name="invoiceTo" value = "<?php echo date('Y/m/d'); ?>" />
	</p>
	<p>Invoice #: <br />
	<input type="text" name="invoiceNumber" value = "<?php echo ("000"); ?>" />
	</p>
	<input type="submit" />
	<input type="hidden" name="invoiceSubmit" />	
	</form>
</div>
