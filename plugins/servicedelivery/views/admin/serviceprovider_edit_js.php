<?php
/**
 * Edit service providers js file.
 *
 * Handles javascript stuff related to edit report function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     ServiceProviders Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
        $(document).ready(function() {

			/* Form Actions */
			// Action on Save Only
			$('.btn_save').live('click', function () {
				$("#save").attr("value", "1");
				$(this).parents("form").submit();
				return false;
			});

			$('.btn_save_close').live('click', function () {
				$(this).parents("form").submit();
				return false;
			});

			// Delete Action
			$('.btn_delete').live('click', function () {
				var agree=confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> <?php echo Kohana::lang('ui_admin.delete_action'); ?>?");
				if (agree){
					$('#reportMain').submit();
				}
				return false;
			});
        });