<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $header_block; ?>
	<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
</head>

<body id="page">
    <!-- header wrapper -->
    <div id="header-wrapper">
    	<!-- header -->
    	<div id="header">
    	    <!--header-left -->
            <div id="header-left">
        		<!-- logo -->
        		<div id="logo">
        		    <!--
        			<h1><?php //echo $site_name; ?></h1>
        			<span><?php //echo $site_tagline; ?></span>
        			-->
        		</div>
        		<!-- / logo -->
        		<div class="clearingfix"></div>
    		</div>
    		<!-- /header-left -->

            <!-- right header -->
            <div id="header-right">
				
				<div id="loginsection">
					<?php print form::input(array('type'=>'button', 'name'=>'d_login_button', 'id'=>'d_login_button', 'class'=>'huduma_button'), Kohana::lang('ui_main.login')); ?>
					<!-- login -->
					<div class="login">
						<?php if ($is_logged_in): ?>
							<?php print form::open(url::site().'dashboards/logout'); ?>
							<div class="row">
								<h3><?php echo $logged_in_user; ?></h3>
								<p class="dashboard_role_detail"><?php echo $static_entity_name; ?></p>
							</div>

							<div class="row login_panel_button">
								<?php print form::input(array('type'=>'submit', 'name'=> 'dashboard_logout', 'class'=>'huduma_button login_panel_button'), Kohana::lang('ui_admin.logout')); ?>
							</div>
							<?php print form::close(); ?>
						<?php else: ?>
							<?php print form::open(url::site().'dashboards/login'); ?>
								<div class="row">
									<h5><?php echo Kohana::lang('ui_huduma.username_or_email'); ?></h5>
									<?php print form::input(array('name' => 'dashboard_username', 'class'=>'field', 'placeholder' => Kohana::lang('ui_huduma.username_placeholder')), ''); ?>
								</div>

								<div class="row">
									<h5><?php echo Kohana::lang('ui_main.password'); ?></h5>
									<?php print form::password(array('name' => 'dashboard_password', 'class'=>'field', 'placeholder' => Kohana::lang('ui_huduma.password_placeholder')), ''); ?>
								</div>

								<div class="row login_panel_button">
									<?php print form::input(array('type' => 'submit', 'name'=>'submit', 'class'=>'huduma_button'), Kohana::lang('ui_huduma.sign_in')); ?>
									<?php print form::input(array('type'=>'checkbox', 'name'=>'dashboard_login_remember', 'id'=>'dashboard_remember'), ''); ?>
									<label for="dashboard_login_remember"><?php echo Kohana::lang('ui_huduma.remember_me'); ?></label>
								</div>
							<?php print form::close(); ?>
						<?php endif; ?>

					</div>
					<!-- /login -->
				</div>

                <!-- mainmenu -->
        		<div id="mainmenu" class="clearingfix">
        			<ul>
        				<?php nav::main_tabs($this_page); ?>
        			</ul>
        		</div>
                <!-- /mainmenu -->
                <div id="clearingfix"></div>
            </div>
            <!-- /right header -->

    	</div>
    	<!-- / header -->
	</div>
	<!-- /header wrapper-->

	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<div id="content-mainbar"></div>
		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">
