<?php
/**
 * View file for the report list items
 *
 */
?>
	<?php if ($reports): ?>
		<?php foreach ($reports as $incident): ?>
		<li class="report_box">
			<div class="dashboard_report_item">
				<div class="report_item_media">
				<?php 
					$image_url = "";
					if ($incident->incident_mode == 1) // Web
					{
						$image_url = url::base().'plugins/huduma/views/images/msDash.png';
					}
					elseif ($incident->incident_mode == 2) // SMS
					{
						$image_url = url::base().'plugins/huduma/views/images/sms.png';
					}
					elseif ($incident->incident_mode == 3) // Twitter
					{
						$image_url = url::base().'plugins/huduma/views/images/twDash.png';
					}
					
					if ( ! empty($image_url))
					{
						print html::image(array('src'=>$image_url, 'border'=>'0'));
					}
				?>
				</div>
				<div class="report_item_content">
					<p>
						<strong>
							<a href="<?php echo url::site().$report_view_controller.$incident->id; ?>"><?php echo $incident->incident_title; ?></a>
						</strong>
					</p>
					<div class="report_item_date">
						<span class="comment_date_time"><?php echo Kohana::lang('ui_huduma.submitted_on'); ?></span>
						<span class="comment_date_time">
							<?php echo date('g:m a', strtotime($incident->incident_date)); ?>
						</span>
						<span class="comment_date_time">
							<?php echo date('F j, Y', strtotime($incident->incident_date)); ?>
						</span>
						<span class="report_comment_info">
							<?php if ($incident->comment_count > 0): ?>
							<?php //TODO: Comments image ?>
							<?php echo $incident->comment_count; ?>
							<span><?php echo Kohana::lang('ui_main.comments'); ?></span>
							<?php endif; ?>
						</span>
					</div>
				</div>
			</div>
				
		</li>
		<?php endforeach; ?>
	<?php endif; ?>