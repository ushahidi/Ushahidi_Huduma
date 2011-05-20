<?php
/**
 * Javascript for the category dashboards
 */
?>
	$(document).ready(function(){
		// Initialize horizontal bar charts
		$(".cat-stats-graph").horizontalBarGraph({interval: 0.55});
		
		$("#emptyFilterResults").css("display", "none");
	});
	
	
	/**
	 * Loads report items from the specified fetch URL by applying the specified filter
	 */
	function loadReportItems(itemId, fetchURL, filter)
	{
		$("#emptyFilterResults").css("display", "none");
		
		// Toggle CSS class
		$(".report-filter-tabs a").removeClass("active");
		$("#"+itemId).addClass("active");
		
		$("div.reports-list-holder").html("");
		$("div.reports-list-holder").fadeOut('slow');
		
		// Get the reports
		$.get(fetchURL + '&filter=' + filter, function(data){
			if (data != "" && data != null && data.length > 0)
			{
				setTimeout(function(){
					$("div.reports-list-holder").fadeIn('fast');
					$("div.reports-list-holder").html(data);
				}, 500);
			}
			else
			{
				// Show message
				setTimeout(function(){
					$("#emptyFilterResults").css("display", "block");
					$("#emptyFilterResults").hide();
					$("#emptyFilterResults").slideDown("slow");
				}, 300);
			}
		});
	}
