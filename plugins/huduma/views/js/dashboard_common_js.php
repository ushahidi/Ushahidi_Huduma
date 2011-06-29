<?php
/**
 * Common javascript for the dashboard pages
 */
?>
	$(document).ready(function(){

		/* Form Actions */
		// Action on Save Only
		$('.btn_save').live('click', function () {
		$("#save").attr("value", "1");
		$(this).parents("form").submit();
		return false;
		});
	});
    
	function updateComment(commentId, containerHide, containerShow, action)  {
		// POST data
		var postData = { comment_id:commentId, action: action };

		// Execute POST
		$.post('<?php echo url::site(); ?>dashboards/home/update_comment', 
			postData, 
			function(response){
				if (response.success) {
					// Hide and show HTML elements
					$("#"+containerHide+"").hide();
					$("#"+containerShow+"").show();
				}
			}
		);
	}
	
	/**
	 * Toggles the visibility of a HTML element with the provided class
	 */
	function toggleItemDisplay(itemShow, itemHide) {
		$('.'+itemHide).hide('fast');
		$('.'+itemShow).css('display', 'block');
		$('.'+itemShow).show('fast');
	}
	
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
