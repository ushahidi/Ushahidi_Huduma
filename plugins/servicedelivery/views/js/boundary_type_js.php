<?php
/** 
 * Boundary type js file. 
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
 
     // Boundary type JS
     function fillFields(id, boundary_type_name, parent_id) {
        $("#boundary_type_id").attr("value", unescape(id));
        $("#boundary_type_name").attr("value", unescape(boundary_type_name));
        $("#parent_id").attr("value", unescape(parent_id));
    }

	// Ajax Submission
	function boundaryTypeAction(action, confirmAction, boundary_type_id )
	{
		var statusMessage;
		if( !isChecked( "boundary_type" ) && boundary_type_id =='' )
		{
			alert('Please select at least one service provider.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				if (boundary_type_id != '')
				{
					// Submit Form For Single Item
					$("#boundary_type_id_single").attr("value", boundary_type_id);
					$("#boundaryTypeListing").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#boundary_type_id_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#boundaryTypeListing").submit();
				}

			} else {
				return false;
			}
		}
	}