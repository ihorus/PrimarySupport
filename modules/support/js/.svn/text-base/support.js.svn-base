$(function() {
	$("#categoryAddSubmit").click(function() {
		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		// description
		var category = $("input#categoryAdd").val();
		// reset the description box css, incase there was an error before hand
		$("input#categoryAdd").css({"background-color": "#FFFFFF"});
		
		if (category == "") {
			$("input#categoryAdd").css({"background-color": "#FFBFBF"});
			$("input#categoryAdd").focus();
			return false;
		}
		
		// url we're going to send the data to
		var url = "modules/support/actions/addCategory.php";
				
		// perform the post to the action (take the info and submit to database)
		$("input#categoryAdd").val("");
		$("input#categoryAdd").attr('disabled', 'disabled');
		
		$.post(url,{
			jobUID: jobUID,
			category: category
		}, function(data){
			$("#categoryFormContainer").append(data);
			
			// remove the loading image
			$("input#categoryAdd").val("");
			var cssObj = {
				'background-color' : '#FFFFFF',
				'textarea:focus' : '#EFFAE6' // this doesn't seem to be working (ABr)
				}
			$("input#categoryAdd").css(cssObj);
			$("input#categoryAdd").removeAttr('disabled');
		},'html');
		
		
		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;
	});
	
});