<?php
/** 
 * Boundaries js file. 
 * 
 * Handles javascript stuff related to boundaries function. 
 * 
 * PHP version 5 
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI: 
 * http://www.gnu.org/copyleft/lesser.html 
 * @author Ushahidi Team <team @ushahidi.com>
 * @package Ushahidi - http://source.ushahididev.com 
 * @module Huduma Controller 
 * @copyright Ushahidi - http://www.ushahidi.com 
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
 ?>

 <?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>

     // Boundaries JS
     function fillFields(id, boundary_name, boundary_type_id, parent_id) {
        $("#boundary_id").attr("value", unescape(id));
        $("#boundary_name").attr("value", unescape(boundary_name));
        $("#boundary_type").attr("value", unescape(boundary_type_id));
        $("#parent_id").attr("value", unescape(parent_id));
    }

	// Ajax Submission
	function boundaryAction(action, confirmAction, boundary_id )
	{
		var statusMessage;
		if( !isChecked( "boundary" ) && boundary_id =='' )
		{
			alert('Please select at least one service provider.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				if (boundary_id != '')
				{
					// Submit Form For Single Item
					$("#boundary_id_single").attr("value", boundary_id);
					$("#boundaryListing").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#boundary_id_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#boundaryListing").submit();
				}

			} else {
				return false;
			}
		}
	}

    /**
     * Shows the hierarchy of the boundary specified in @param boundary_id
     * The child items are appended to the element with id @param element_id
     */
    function showHierarchy(boundary_id, parent_element_id)
    {
        // Hide the element to contain the children
        var parent_css_id = '#'+parent_element_id;
        
        // Hide the parent element if visible
        if ($(parent_css_id).css('visibility') != 'hidden')
        {
            $(parent_css_id).slideUp();
            $(parent_css_id).html("");
            $('#arrow_'+boundary_id).html("&rarr;");
            $(parent_css_id).css('visibility', 'hidden');
            return;
        }

        $.getJSON('./json/boundaries/children/'+boundary_id,
                function(data){
                    if (data != null && data.content != null) {
                        // Iterate over the response content and generate the HTML
                        var html = "<ul class=\"boundary-list\">";
                        
                        $.each(data.content, function(key, value){
                            // Generate the CSS id for the element to hold the children for te current item
                            var element_css_id = "boundary_"+key;
                            var href_value = "javascript:showHierarchy('"+key+"','"+element_css_id+"')";

                            html += "<li>";
                            html += "<span id=\"arrow_"+key+"\">&rarr;</span>";
                            html += "<span><a href=\""+href_value+"\">"+value+"</a></span>";
                            html += "<div id=\""+element_css_id+"\" style=\"visibility:hidden\"></div>";
                            html += "</li>";
                        });
                        
                        html += "</ul>";

                        $(parent_css_id).html(html);
                        $('#arrow_'+boundary_id).html("&darr;");
                        $(parent_css_id).css('visibility', 'visible');
                        $(parent_css_id).hide();
                        $(parent_css_id).slideDown();
                    }
                }
        );
    }