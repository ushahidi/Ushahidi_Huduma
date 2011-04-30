<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
			<h2 class="dialog-title"><?php echo
			strtoupper(Kohana::lang('ui_huduma.add_static_entity_metadata')); ?></h2>

			<!-- messages -->
			<div id="message_green_box" style="display: none;"></div>
			<div id="message_red_box" style="display: none;"></div>
			<!-- /messages -->

			<?php print form::open(); ?>

				<?php print form::hidden('static_entity_type_action', 'a'); ?>
				<input type="hidden" name="static_entity_type_metadata_id" id="static_entity_type_metadata_id" value="<?php echo $dialog_form['id']; ?>" >

				<div class="sms-holder">

					<div class="row">
						<h4><?php echo Kohana::lang('ui_huduma.entity_type'); ?></h4>
						<?php print form::dropdown('metadata_static_entity_type_id',$entity_types,$dialog_form['static_entity_type_id']); ?>
					</div>

					<div class="row">
						<h4><?php echo Kohana::lang('ui_main.name'); ?></h4>
						<?php print form::input('metadata_name', $dialog_form['metadata_item'], ' class="text" '); ?>
					</div>

					<div class="row">
						<h4><?php echo Kohana::lang('ui_main.description'); ?></h4>
						<?php print form::input('metadata_description', $dialog_form['description'], ' class="text long2"'); ?>
					</div>

				</div>

				<div class="btns">
					<ul>
						<li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
						<li><a href="<?php echo url::site().'admin/entities/types';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
					</ul>
				</div>

			<?php print form::close(); ?>
