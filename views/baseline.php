<div class="navbar navbar-fixed-bottom hidden-phone">
	<div class="navbar-inner">
		<div class="container">
			<p class="pull-left">
				&nbsp;&copy;&nbsp;2005 - <?php echo date("Y"); ?> ICT Facilities, <a href="http://www.wallingford.oxon.sch.uk">Wallingford School</a> <i>(version <?php echo SITE_VERSION; ?>)</i>
				Code licensed under GPL 3.0
			</p>
			<p class="pull-right">
				<a href="index.php">Home</a> |
				<a href="<?php echo ("login.php?logout=TRUE"); ?>">Logout</a> |
				<a href="#">Back to top <i class="icon-chevron-up icon-white"></i></a>
			</p>
		</div>
	</div>
</div>

<script src="js/jquery-1.7.1.min.js"></script>
<script src="js/jquery-ui-1.8.18.custom.min.js"></script>
<script src="js/bootstrap.js"></script>

<script>
$(".test123").mouseover(function() {
	//alert('test');
	$(this).popover('show')
});
</script>