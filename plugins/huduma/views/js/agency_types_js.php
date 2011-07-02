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

	// Populates the input fields in the agency types form
	function fillFields(id, type_name, short_name) {
		show_addedit();
		$("#agency_type_id").attr("value", unescape(id));
		$("#type_name").attr("value", unescape(type_name));
		$("#short_name").attr("value", unescape(short_name));
	}

	// Ajax Submission
	function agencyTypeAction(action, confirmAction, agency_type_id)
	{
		var statusMessage;
		if( !isChecked( "agency_type" ) && agency_type_id =='' )
		{
			alert('Please select at least one agency.');
		} 
		else 
		{
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer)
			{
				// Set Submit Type
				$("#action").attr("value", action);

				if (agency_type_id != '')
				{
					// Submit Form For Single Item
					$("#agency_type_id_single").attr("value", agency_type_id);
					$("#agencyTypeListing").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#agency_type_id_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#agencyTypeListing").submit();
				}

			}
			else
			{
				return false;
			}
		}
	}