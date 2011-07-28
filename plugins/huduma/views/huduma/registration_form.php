<?php
/**
 * Registration form view page
 */
?>
	<div class="registration_form">
		<?php print form::open(NULL, array('id' => 'registration-form', 'name' => 'registration-form')); ?>
			<div class="row">
				<h1><?php echo strtoupper(Kohana::lang('ui_huduma.registration_form')); ?></h1>
			</div>
			
			<div id="submitStatus" style="display:none;"></div>
			
			<div class="row">
				<span>*<?php echo Kohana::lang('ui_huduma.required_field'); ?></span>
			</div>
			<table>
				<tbody>
					<tr>
						<td class="label">* <?php echo Kohana::lang('ui_huduma.full_name'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::input('first_name', NULL, ' class="text medium" placeholder="'.Kohana::lang('ui_huduma.first_name_placeholder').'"'); ?>
								<?php print form::input('last_name', NULL, ' class="text small" placeholder="'.Kohana::lang('ui_huduma.last_name_placeholder').'"'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label">* <?php echo Kohana::lang('ui_huduma.phone_no'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::input('phone_number', NULL, ' class="text medium" placeholder="'.Kohana::lang('ui_huduma.phone_placeholder').'"'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label">* <?php echo Kohana::lang('ui_main.email'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::input('email', NULL, ' class="text medium" placeholder="'.Kohana::lang('ui_huduma.email_placeholder').'"'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label">* <?php echo Kohana::lang('ui_huduma.agency_type'); ?>:</td>
						<td>
							<div class="field_container">
							<?php foreach ($agency_types as $key => $value): ?>
								<span class="radio_label">
								<input type="radio" name="agency_type_id" id="agency_type_<?php echo $key; ?>" value="<?php echo $key; ?>">
								<label for="agency_type_<?php echo $key;?>"><?php echo $value; ?></label>
								</span>
							<?php endforeach; ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo Kohana::lang('ui_huduma.county'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::dropdown('county_id', $counties, NULL); ?>
								<?php print form::dropdown('constituency_id', '---'.Kohana::lang('ui_huduma.select_constituency').'---', ''); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo Kohana::lang('ui_main.category'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::dropdown('category_id', $categories, NULL); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo Kohana::lang('ui_huduma.facility'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::dropdown('facility_type_id', $facility_types, NULL, ' style="width: 170px;"'); ?>
								<?php print form::dropdown('facility_id', $facilities, NULL, ' style="width: 280px;"'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo Kohana::lang('ui_huduma.incharge'); ?>:</td>
						<td>
							<div class="field_container">
								<?php print form::checkbox('in_charge', 0, FALSE, ' disabled'); ?>
							</div>
						</td>
					</tr>
					<tr>
						<td class="label"><?php echo Kohana::lang('ui_main.security_code'); ?>:</td>
						<td>
							<div class="field_container">
								<div style="padding: 3px 0;"><?php echo $captcha; ?></div>
								<?php print form::input('security_code', NULL, ' class="text small" placeholder="'.Kohana::lang('ui_main.security_code').'"'); ?>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="row">
				<input type="button" class="btn_submit" id="register_user" value="<?php echo strtoupper(Kohana::lang('ui_huduma.submit')); ?>">
				<a href="#" id="close_registration_form" class="cancel"><?php echo Kohana::lang('ui_main.cancel'); ?></a>
			</div>
		<?php print form::close(); ?>
	</div>