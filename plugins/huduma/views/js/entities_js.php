<?php
/**
 * Static entities JavaScript file
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

	// Ajax Submission
	function entityAction( action, confirmAction, static_entity_id )
	{
		var statusMessage;
		if( !isChecked( "static_entity" ) && static_entity_id =='' )
		{
			alert('Please select at least one service provider.');
		} else {
			var answer = confirm('<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> ' + confirmAction + '?')
			if (answer){
				// Set Submit Type
				$("#action").attr("value", action);

				if (static_entity_id != '')
				{
					// Submit Form For Single Item
					$("#static_entity_single").attr("value", static_entity_id);
					$("#entityListMain").submit();
				}
				else
				{
					// Set Hidden form item to 000 so that it doesn't return server side error for blank value
					$("#static_entity_single").attr("value", "000");

					// Submit Form For Multiple Items
					$("#entityListMain").submit();
				}

			} else {
				return false;
			}
		}
	}