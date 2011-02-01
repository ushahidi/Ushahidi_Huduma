<?php
/**
 * Static Entity Types js file.
 *
 * Handles javascript stuff related  to api log function
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Staticentity Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>

<?php require SYSPATH.'../application/views/admin/form_utils_js.php' ?>

    // Entity types JS
    function fillFields(id, type_name, category_id, entity_type_color)
    {
        show_addedit();
        $("#entity_type_id").attr("value", unescape(id));
        $("#type_name").attr("value", unescape(type_name));
        $("#category_id").attr("value", unescape(category_id));
        $("#entity_type_color").attr("value", unescape(entity_type_color));
    }

	// Ajax Submission
	function entityTypeAction( action, confirmAction, entity_type_id )
	{
		var statusMessage;
		if( !isChecked( "entity_type" ) && entity_type_id =='' )
		{
			alert('Please select at least one entity type.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				if (entity_type_id != '')
				{
					// Submit Form For Single Item
					$("#entity_type_single").attr("value", entity_type_id);
					$("#entityTypeListing").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#entity_type_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#entityTypeListing").submit();
				}

			} else {
				return false;
			}
		}
	}