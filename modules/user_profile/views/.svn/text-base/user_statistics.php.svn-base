<?php
require_once("../../../engine/initialise.php");
$user = User::find_by_uid($_GET['uUID']);
?>
<script type="text/javascript">
$(function() {
	// jobUID and month fudge values
	var uUID = $("input#uUID").val();
	var month = "All";
	var url = "modules/user_profile/views/user_statistics_sub.php";
	
	$.post(url,{
		uUID: uUID,
		month: month
	}, function(data){
		$("#test2").html(data);
	},'html');

	$("#monthSelected").change(function() {
		// validate and process form here
		
		// month
		var month = $("select#monthSelected").val();
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			uUID: uUID,
			month: month
		}, function(data){
			$("#test2").html(data);
		},'html');
		return false;
	})
	
})
</script>
<form>
<div id="test2"></div>
<input type="hidden" id="uUID" value="<?php echo $user->uid; ?>">
<?php
// build an array on months
$mon = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$current = date('n');

$i = 5;
$monthNum = date('n');
$yearNum = date('Y');
do {
    $months[] = date('Y, M', strtotime($yearNum . "-" . $monthNum));
    
    if ($monthNum == 1) {
    	$monthNum = 12;
    	$yearNum = $yearNum-1;
    } else {
    	$monthNum = $monthNum - 1;
    }
    
    $i = $i - 1;
} while ($i > 0);

$form .= "<select id=\"monthSelected\" >";
$form .= optionDropdown("All", "All", $_POST['monthSelected']);
foreach ($months AS $month) {
    $form .= optionDropdown($month, $month, $_POST['monthSelected']);
}

$form .= "</select>";
echo $form;
?>
</form>