<?php
/**
 * Javascript for the category dashboards
 */
?>
	<?php require_once(PLUGINPATH.'huduma/views/js/dashboard_common_js.php'); ?>
	$(document).ready(function(){
		// Initialize horizontal bar charts
		$(".cat-stats-graph").horizontalBarGraph({interval: 0.55});
		
		$("#emptyFilterResults").css("display", "none");
	});