<?php
/**
 * View for the dashboard reports
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
<div id="entity_view_column">
	<div class="dash-page-header">
		<h3><?php echo strtoupper(Kohana::lang('ui_main.reports')); ?></h3>
	</div>
	<ul class="reports-list">
		<?php echo $report_list_items; ?>
	</ul>
</div>