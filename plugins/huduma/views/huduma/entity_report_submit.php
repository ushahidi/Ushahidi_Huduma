<?php
/**
 * View page for submitting reports for a statuc entity
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
    <div style="margin: 0 3%; width: 100%">
        <?php print form::open(NULL, array('id'=>'entityReportForm', 'name'=>'entityReportForm')); ?>
        	
        	<?php if ($form_error) : ?>
        	<!-- red-box -->
            <div class="red-box">
        		<h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
        		<ul>
                 <?php foreach($errors as $error_item => $description): ?>
                 	<?php print (!$description)? "" : "<li>".$description."</li>"; ?>
        		<?php endforeach; ?>
        		</ul>
        	</div>
        	<!-- /red-box -->
        	<?php endif; ?>

        	<!-- green-box -->
        	<div class="green-box" id="submitStatus" style="display:none;">
        		<h3><?php echo Kohana::lang('ui_main.report_submitted'); ?></h3>
        	</div>
        	<!-- /green-box-->
        	
        	<div class"entiy_report">
				<div class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_title'); ?></h4>
					<?php print form::input('incident_title', $form['incident_title'], ' class="text long"'); ?>
				</div>
				<div class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_description'); ?></h4>
					<?php print form::textarea('incident_description', $form['incident_description'], ' rows="6" class="textarea long" ') ?>
				</div>
				<div class="report_row" id="datetime_default">
					<h4><a href="#" id="date_toggle" class="show-more"><?php echo Kohana::lang('ui_main.modify_date'); ?></a><?php echo Kohana::lang('ui_main.date_time'); ?>: 
						<?php echo Kohana::lang('ui_main.today_at')." "."<span id='current_time'>".$form['incident_hour']
							.":".$form['incident_minute']." ".$form['incident_ampm']."</span>"; ?></h4>
				</div>
				<div class="report_row hide" id="datetime_edit">
					<div class="date-box">
						<h4><?php echo Kohana::lang('ui_main.reports_date'); ?></h4>
						<?php print form::input('incident_date', $form['incident_date'], ' class="text short"'); ?>								
						<script type="text/javascript">
							$().ready(function() {
								$("#incident_date").datepicker({ 
									showOn: "both", 
									buttonImage: "<?php echo url::base() ?>media/img/icon-calendar.gif", 
									buttonImageOnly: true 
								});
							});
						</script>
					</div>
					<div class="time">
						<h4><?php echo Kohana::lang('ui_main.reports_time'); ?></h4>
						<?php
							for ($i=1; $i <= 12 ; $i++) { 
								$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);	 // Add Leading Zero
							}
							for ($j=0; $j <= 59 ; $j++) { 
								$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);	// Add Leading Zero
							}
							$ampm_array = array('pm'=>'pm','am'=>'am');
							print form::dropdown('incident_hour',$hour_array,$form['incident_hour']);
							print '<span class="dots">:</span>';
							print form::dropdown('incident_minute',$minute_array,$form['incident_minute']);
							print '<span class="dots">:</span>';
							print form::dropdown('incident_ampm',$ampm_array,$form['incident_ampm']);
						?>
					</div>
					<div style="clear:both; display:block;" id="incident_date_time"></div>
				</div>
				<script type="text/javascript">
					var now = new Date();
					var h=now.getHours();
					var m=now.getMinutes();
					var ampm="am";
					if (h>=12) ampm="pm"; 
					if (h>12) h-=12;
					var hs=(h<10)?("0"+h):h;
					var ms=(m<10)?("0"+m):m;
					$("#current_time").text(hs+":"+ms+" "+ampm);
					$("#incident_hour option[value='"+hs+"']").attr("selected","true");
					$("#incident_minute option[value='"+ms+"']").attr("selected","true");
					$("#incident_ampm option[value='"+ampm+"']").attr("selected","true");
				</script>
        	</div>
        	
			<div class="report_optional" style="width: 87%;">
				<h3><?php echo Kohana::lang('ui_main.reports_optional'); ?></h3>
				<div class="report_row">
						 <h4><?php echo Kohana::lang('ui_main.reports_first'); ?></h4>
						 <?php print form::input('person_first', $form['person_first'], ' class="text long"'); ?>
				</div>
				<div class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_last'); ?></h4>
					<?php print form::input('person_last', $form['person_last'], ' class="text long"'); ?>
				</div>
				<div class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_email'); ?></h4>
					<?php print form::input('person_email', $form['person_email'], ' class="text long"'); ?>
				</div>
			</div>
        	
        	<div class="row">
        	    <input name="submit" class="btn_submit" id="report_submit" type="button" value="<?php echo Kohana::lang('ui_main.reports_btn_submit'); ?>" class="huduma_button" /> 
        	</div>
        <?php print form::close(); ?>
    </div>