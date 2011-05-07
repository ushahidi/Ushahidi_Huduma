<?php
/**
 * View file for the static entity comments
 */
?>
<div id="entity_view_column">
	<div class="dash-page-header">
		<h3><?php echo strtoupper(Kohana::lang('ui_main.reports')); ?></h3>
	</div>
	<ul class="reports-list">
		<!-- comments -->
	<?php if ($reports): ?>
		<?php foreach ($reports as $incident): ?>
		<li class="report-box" id="dashboard_comment_<?php echo $incident->id; ?>">
			<div class="dashboard-report-item">
				<div class="report-item-status">
				</div>
				<div class="report-item-separator" style="clear:both;"></div>
				<div class="report-item-content">
					<strong><a href="<?php echo url::site().$report_view_controller.$incident->id; ?>"><?php echo $incident->incident_title; ?></a></strong>
				</div>
				<div class="report-item-separator" style="clear:both;"></div>
				<div class="report-item-date">
					<span class="comment_date_time"><?php echo Kohana::lang('ui_huduma.submitted_on'); ?></span>
					<span class="comment_date_time">
						<?php echo date('g:m a', strtotime($incident->incident_date)); ?>
					</span>
					<span class="comment_date_time">
						<?php echo date('F j, Y', strtotime($incident->incident_date)); ?>
					</span>
					<div class="report-item-action">
						<div class="report-item-action-box" id="cloader_<?php echo $incident->id; ?>">
							<?php if ($incident->comment->count() > 0): ?>
							<?php //TODO: Comments image ?>
							<?php echo $incident->comment->count(); ?>
							<span><?php echo Kohana::lang('ui_main.comments'); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
				
		</li>
		<?php endforeach; ?>
	<?php endif; ?>
	<!-- /comments -->
	</ul>
</div>