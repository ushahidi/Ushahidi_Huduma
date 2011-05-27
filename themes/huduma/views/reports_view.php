<div class="bg">
	
	<div class="dashboard-title">
		<h1>
			<?php echo Kohana::lang('ui_huduma.report_details'); ?>
		</h1>
	</div>
	<div style="clear: both;"></div>
	<div id="pageColLeft">
		<div id="sidebar-left-content">
			<!-- dashboard menu -->
			<!-- /dashboard menu -->
			
			<div class="report-additional-reports">
				<h4><?php echo strtoupper(Kohana::lang('ui_main.additional_reports'));?></h4>
				<?php foreach($incident_neighbors as $neighbor): ?>
				<div class="rb_report">
					<h5><a href="<?php echo url::site(); ?>reports/view/<?php echo $neighbor->id; ?>"><?php echo $neighbor->incident_title; ?></a></h5>
					<p class="r_date r-3 bottom-cap"><?php echo date('H:i M d, Y', strtotime($neighbor->incident_date)); ?></p>
					<p class="r_location"><?php echo $neighbor->location_name.", ".round($neighbor->distance, 2); ?> Kms</p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	
	<div id="pageColRight">
		<div class="dashboard_container">
			<div id="report-map" style="margin: 10px 0 30px 0;">
				<div id="map" style="width:575px; height: 250px;"></div>
				<div style="clear:both"></div>
			</div>
		
			<div class="report-header">
				<div class="report-item-date">
					<span class="comment_date_time"><?php echo Kohana::lang('ui_huduma.submitted_on'); ?></span>
					<span class="comment_date_time">
						<?php echo date('g:m a', strtotime($incident_date)); ?>
					</span>
					<span class="comment_date_time">
						<?php echo date('F j, Y', strtotime($incident_date)); ?>
					</span>
				</div>
				<div class="row" style="margin:5px 0;">
					<span class="comment_date_time"><?php echo Kohana::lang('ui_main.location').': '.$incident_location; ?></span>
				</div>
				<div class="row">
					<h3><?php echo $incident_title; ?></h3>
				</div>
			</div>
		
			<?php 
				//TODO: Run event here to show the ticket status info
				Event::run('ushahidi_action.report_meta', $incident_id);
			?>
		
			<div style="clear:both;"></div>
		
			<div class="description">
				<?php echo $incident_description; ?>
			</div>
		
			<?php echo navigator::inline_comments($incident_id); ?>
		
			<?php
				Event::run('ushahidi_action.report_extra', $incident_id);
			 ?>
		
		</div>
	</div>
	
</div>
