<div id="content">
	<div class="content-bg">
		<div class="page-title"><h1><?php echo Kohana::lang('ui_main.alerts'); ?></h1></div>
		<div style="clear: both;"></div>
		
		<!-- start block -->
		<div class="big-block">
			<div id="pageColRight">
			
			<?php if($show_mobile == TRUE):  ?>
				<!-- Mobile Alert -->
				<div class="green-box">
				<?php if ($alert_mobile): ?>
					<h3><?php echo Kohana::lang('alerts.mobile_ok_head'); ?></h3>
				<?php endif; ?>
					<div class="alert_response">
					<?php if ($alert_mobile): ?>
					<?php	echo Kohana::lang('alerts.mobile_alert_request_created')."<u><strong>"
								. $alert_mobile."</strong></u>."
								. Kohana::lang('alerts.verify_code');
					?>
					<?php endif; ?>
						<div class="alert_confirm">
							<div class="label">
								<u><?php echo Kohana::lang('alerts.mobile_code'); ?></u>
							</div>
							<?php 
							print form::open('/alerts/verify');
							print "Verification Code:<BR>".form::input('alert_code', '', ' class="text"')."<BR>";
							print "Mobile Phone:<BR>".form::input('alert_mobile', $alert_mobile, ' class="text"')."<BR>";
							print form::submit('button', 'Confirm My Alert Request', ' class="btn_submit"');
							print form::close();
							?>
						</div>
					</div>
				</div>
				<!-- / Mobile Alert -->
			<?php endif; ?>
			
				<!-- Email Alert -->
				<div class="green-box">
					<?php if ($alert_email): ?>
						<h3><?php echo Kohana::lang('alerts.email_ok_head'); ?></h3>
					<?php endif; ?>
				
					<div class="alert_response">
						<?php  if ($alert_email)
						{
							echo Kohana::lang('alerts.email_alert_request_created')."<u><strong>".
								$alert_email."</strong></u>.".
								Kohana::lang('alerts.verify_code');
						}
						?>
						<div class="alert_confirm">
							<div class="label">
								<u><?php echo Kohana::lang('alerts.email_code'); ?></u>
							</div>
							<?php 
							print form::open('/alerts/verify');
							print "Verification Code:<BR>".form::input('alert_code', '', ' class="text"')."<BR>";
							print "Email Address:<BR>".form::input('alert_email', $alert_email, ' class="text"')."<BR>";
							print form::submit('button', 'Confirm My Alert Request', ' class="btn_submit"');
							print form::close();
							?>
						</div>
					</div>
				</div>
				<!-- / Email Alert -->
			
				<!-- Return -->
				<div class="green-box">
					<div class="alert_response">
						<a href="<?php echo url::site().'alerts'?>">
						<?php echo Kohana::lang('alerts.create_more_alerts'); ?>
						</a>
					</div>
				</div>
				<!-- / Return -->
			</div>
		</div>
		<!-- end block -->
	</div>
</div>