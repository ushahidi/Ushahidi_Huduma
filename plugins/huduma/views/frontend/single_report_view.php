<?php
/**
 * View file for a single static entity report
 *
 * Handles javascript stuff related to entity view function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Entities Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
	<div class="bg">
		<div id="content-section-left">
			<div id="sidebar-left-content">
				<?php if ($show_dashboard_panel): ?>
					<?php echo $dashboard_panel; ?>
				<?php else: ?>
					<div class="row">&nbsp;</div>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="report-form">
			<div class="entity-name">
			</div>
		</div>
		
		<div class="dashboard_container">
			<div class="report-header">
				<div class="report-item-date">
					<span class="comment_date_time"><?php echo Kohana::lang('ui_huduma.submitted_on'); ?></span>
					<span class="comment_date_time">
						<?php echo date('g:m a', strtotime($incident->incident_date)); ?>
					</span>
					<span class="comment_date_time">
						<?php echo date('F j, Y', strtotime($incident->incident_date)); ?>
					</span>
				</div>
				<div class="row">
					<h3><?php echo $incident->incident_title; ?></h3>
				</div>
			</div>
			<?php if ($ticket AND ! empty($ticket)): ?>
			<div class="report-ticket-info">
				<span class="state state-<?php echo ($ticket->report_status_id==1)? 'open' : 'closed'; ?>">
					<?php echo $ticket->report_status->status_name; ?>
				</span>
				<p><strong><?php echo $incident->comment->count().' '; ?></strong><?php echo strtolower(Kohana::lang('ui_main.comments'));?></p>
			</div>
			<?php endif; ?>
			<div style="clear:both;"></div>
			
			<div class="description">
				<?php echo $incident->incident_description; ?>
			</div>
			<?php echo navigator::inline_comments($incident->id); ?>
			
			<?php echo $comments_form; ?>
		</div>
	</div>
