<script src="js/jquery-1.5.1.min.js"></script>

<script type="text/javascript">
$(function() {
	$("#updateDetailsButton").click(function() {
		// validate and process form here
		// userUID
		var schoolUID = $("select#schoolUID").val();
		var output = "";
		
		$('select#schoolUID option:selected').each(function(index) {
			output += $(this).val() + ",";
		});
		alert(output);
		if (schoolUID == "") {
			alert("There is no user UID specified.  Please contact an Administrator.");
			return false;
		}
		return false;	})
})
</script>


<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
<select name="schoolUID[]" id="schoolUID[]" multiple="multiple">
	<option value="one">Option One</option>
	<option value="two">Option Two</option>
	<option value="three">Option Three</option>
	<option value="four">Option Four</option>
	<option value="five">Option Five</option>
</select>
<input type="submit" id="updateDetailsButton" value="Send" />
</form>