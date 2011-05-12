<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
			<h2 class="dialog-title"><?php echo strtoupper(Kohana::lang('ui_huduma.add_edit_dashboard_role')); ?></h2>

			<!-- messages -->
			<div id="message_green_box" style="display: hidden;"></div>
			<div id="message_red_box" style="display: hidden;"></div>
			<!-- /messages -->

			<?php print form::open(); ?>

				<?php print form::hidden('role_action', 'a'); ?>
				<input type="hidden" name="role_id"  id="role_id" value="<?php echo $form['id']; ?>" >

				<div class="sms-holder">

					<div class="row">
						<h4><?php echo Kohana::lang('ui_main.name'); ?></h4>
						<?php print form::input('role_name', $form['name'], ' class="text" '); ?>
					</div>

					<div class="row">
						<h4><?php echo Kohana::lang('ui_main.description'); ?></h4>
						<?php print form::input('role_description', $form['description'], ' class="text long2"'); ?>
					</div>

					<div class="row">
						<h4><?php echo Kohana::lang('ui_huduma.associated_service_agency'); ?></h4>
						<?php print form::dropdown('role_agency_id', $agencies, $form['agency_id']); ?>
					</div>
				</div>

				<h3><?php echo strtoupper(Kohana::lang('ui_huduma.role_privileges')); ?></h3>
				<div class="row" id="role-category-selector" style="<?php echo $category_container_css; ?>">
					<h4><?php echo Kohana::lang('ui_main.category'); ?></h4>
					<?php print form::dropdown('role_category_id', $categories, $form['category_id']); ?>
				</div>
				<div class="row">
					<h4><?php echo Kohana::lang('ui_huduma.entity_name'); ?></h4>
					<?php print form::dropdown('role_static_entity_id', $entities, $form['static_entity_id']); ?>
				</div>

				<div class="row">
					<h4><?php echo Kohana::lang('ui_huduma.boundary'); ?></h4>
					<?php print form::dropdown('role_boundary_id', $boundaries, $form['boundary_id']); ?>
				</div>

				<div class="row">
					<h4>
						<?php print form::checkbox('role_can_close_issue', $form['can_close_issue'], $form['can_close_issue']); ?>
						<?php echo Kohana::lang('ui_huduma.can_close_issue'); ?>
					</h4>
				</div>
				
				<div class="btns">
					<ul>
						<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
						<li><a href="<?php echo url::site().'admin/dashboard/roles';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
					</ul>
				</div>

			<?php print form::close(); ?>
