<?php
/**
 * View file for the dashboard menu panel
 */
?>
<div class="nav_dashboard_panel">
	<ul class="dashboard_menu">
		<li>
			<a href="<?php echo url::site().'dashboards/home'; ?>">
				<?php echo Kohana::lang('ui_huduma.dashboard_home'); ?>
				<img src="<?php echo url::base().'plugins/huduma/views/images/homeicon.png'?>" border="0">
			</a>
		</li>

		<!-- panel items for static entity role only -->
		<?php if ($static_entity_panel): ?>
		<?
		/*
		<li><a href="<?php echo url::site().'dashboards/home/entity_profile'; ?>"><?php echo Kohana::lang('ui_huduma.entity_profile'); ?></a></li>
		*/
		?>
		<?php endif; ?>
		
		<!-- menu items for boundary level role -->
		<?php if ($boundary_panel): ?>
		<?php
		/*
		<li><a href="<?php echo url::site().'dashboards/home/boundary_profile'; ?>"><?php echo $boundary_type_name.' '.Kohana::lang('ui_huduma.profile'); ?></a></li>
		*/
		?>
		<?php endif; ?>
		
		<!-- menu items for category-level role-->

		<!-- menu items for agency level role -->

		<li><a href="<?php echo url::site().'dashboards/home/change_password'; ?>"><?php echo Kohana::lang('ui_huduma.change_password'); ?></a></li>
	</ul>
</div>