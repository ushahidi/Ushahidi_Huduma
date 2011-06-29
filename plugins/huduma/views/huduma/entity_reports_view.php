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
		<?php if ($reports->count() > 0): ?>
		<div id="entity_view_column">
			<div class="dash-page-header">
				<h3><?php echo strtoupper(Kohana::lang('ui_main.reports')); ?></h3>
				<?php if ($pagination->total_pages > 1): ?>
				<div class="main-reports-pager">
					<?php echo $pagination; ?>
				</div>
				<?php endif; ?>
			</div>
			<ul class="reports-list">
				<?php foreach ($reports as $incident): ?>
				<li class="report_box">
					<div class="dashboard_report_item">
						<div class="report_item_media">
						<?php 
							$image_url = "";
							$mode_name = 'Web';
							if ($incident->incident_mode == 1) // Web
							{
								$image_url = url::base().'plugins/huduma/views/images/msDash.png';
								$mode_name = 'Web';
							}
							elseif ($incident->incident_mode == 2) // SMS
							{
								$image_url = url::base().'plugins/huduma/views/images/sms.png';
								$mode_name = Kohana::lang('ui_main.sms');
							}
							elseif ($incident->incident_mode == 3) // Twitter
							{
								$image_url = url::base().'plugins/huduma/views/images/twDash.png';
								$mode_name = Kohana::lang('ui_main.twitter');
							}

							if ( ! empty($image_url))
							{
								print html::image(array('src'=>$image_url, 'border'=>'0'));
							}
							$mode_name = strtolower($mode_name);
						?>
						</div>
						<div class="report-item-content">
							<h1><?php echo navigator::get_incident_age($incident->incident_date).' '.Kohana::lang('ui_main.via').' '.$mode_name; ?></h1>
							<p>
								<a href="<?php echo url::site().$report_view_controller.$incident->id; ?>"><?php echo $incident->incident_title; ?></a>
							</p>
							<ul class="report-item-actions">
								<li><a href="#"><?php echo Kohana::lang('ui_main.comments').' ('.$incident->comment_count.')'; ?></a></li>
								<li><a href="#"><?php echo Kohana::lang('ui_main.share')?></a></li>
							</span>
							<?php
							/*
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
							*/
							?>
						</div>
					</div>

				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php else: ?>
		<div style="padding: 30px 15px; text-align: center;">
			<p><?php echo Kohana::lang('ui_huduma.no_facility_reports')?></p>
		</div>
		<?php endif; ?>
		