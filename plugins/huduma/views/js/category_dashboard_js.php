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
		
		$("ul.reports-list").html("");
		$("ul.reports-list").fadeOut('slow');
		
		// Get the reports
		$.get(fetchURL + '&filter=' + filter, function(data){
			if (data != "" && data != null && data.length > 0)
			{
				setTimeout(function(){
					$("ul.reports-list").fadeIn('fast');
					$("ul.reports-list").html(data);
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
