<?php
/**
 * View file for displaying the list of agencies to assign a report
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma plugin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		<div class="ticket-box">
			<?php print form::hidden('ticket_id', $form['ticket_id']); ?>
			<div class="row">
				<h4><?php echo Kohana::lang('ui_huduma.agency_to_action'); ?></h4>
				<?php print form::dropdown('report_agency_id', $agencies, $form['report_agency_id']); ?>
			</div>
			<div class="row">
				<h4><?php echo Kohana::lang('ui_huduma.status'); ?></h4>
				<?php print form::dropdown('report_status_id', $report_status, $form['report_status_id']); ?>
			</div>
			<div class="row">
				<h4><?php echo Kohana::lang('ui_huduma.priority'); ?></h4>
				<?php print form::dropdown('report_priority_id', $report_priority, $form['report_priority_id']); ?>
			</div>
			<div class="ticket-notes">
				<a href="javascript:showTicketNotes('ticket_notes_<?php echo $incident_id; ?>')"><?php echo Kohana::lang('ui_huduma.notes'); ?></a>
			</div>
		</div>
		