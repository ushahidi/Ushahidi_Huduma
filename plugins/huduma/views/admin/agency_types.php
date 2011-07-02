<?php
/**
 * Agency Types  view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Huduma - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<div class="bg">
				<h2>
					<?php navigator::subtabs("agencies"); ?>
				</h2>

				<!-- tabs -->
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="<?php echo url::site() ?>admin/agencies"><?php echo Kohana::lang('ui_huduma.view_agencies'); ?></a></li>
						<li><a href="<?php echo url::site() ?>admin/agencies/edit"><?php echo Kohana::lang('ui_huduma.add_edit_agency');?></a></li>
						<li><a href="<?php echo url::site() ?>admin/agencies/types" class="active"><?php echo Kohana::lang('ui_huduma.agency_types');?></a></li>
					</ul>
					
					<div class="tab">
	           			<ul>
	           				<li><a href="#" onclick="agencyTypeAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
	           				<li><a href="#" onclick="agencyTypeAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
	           			</ul>
	           		</div>
				</div>
				
				<?php if ($form_error): ?>
                <!-- red-box -->
                <div class="red-box">
                    <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                    <ul>
                    <?php
                    foreach ($errors as $error_item => $error_description)
                    {
                        print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
                    }
                    ?>
                    </ul>
                </div>
                <?php endif; ?>

				<?php if ($form_saved): ?>
				<!-- green-box -->
				<div class="green-box">
					<h3><?php echo Kohana::lang('ui_huduma.agency_type');?> <?php echo $form_action; ?></h3>
				</div>
				<?php endif; ?>
                
                
				<!-- report-table -->
				<div class="report-form">
					<?php print form::open(NULL,array('id' => 'agencyTypeListing','name' => 'agencyTypeListing')); ?>
						<input type="hidden" name="action" id="action" value="">
						<input type="hidden" name="agency_type_id[]" id="agency_type_id_single" value="">

						<div class="table-holder">
							<table class="table">
								<thead>
									<tr>
										<th class="col-1"></th>
										<th class="col-2"><?php echo Kohana::lang('ui_main.name'); ?></th>
										<th class="col-3"><?php echo Kohana::lang('ui_huduma.short_name'); ?></th>
										<th class="col-4"><?php echo Kohana::lang('ui_admin.actions'); ?></th>
									</tr>
								</thead>
								<tfoot>
									<tr class="foot">
										<td colspan="4"><?php echo $pagination; ?></td>
									</tr>
								</tfoot>
								<tbody>
								
								<?php if ($total_items == 0): ?>
								<tr>
									<td colspan="4" class="col"><h3><?php echo Kohana::lang('ui_main.no_results');?></h3></td>
								</tr>
								<?php endif; ?>
								
								<?php foreach ($agency_types as $agency_type): ?>
								<tr>
									<td class="col-1">
										<input name="agency_type_id[]" id="agency_type" value="<?php echo $agency_type->id; ?>" type="checkbox" class="check-box"/>
									</td>
									<td class="col-2"><?php echo $agency_type->type_name; ?></td>
									<td class="col-3"><?php echo $agency_type->short_name; ?>
									</td>
									<td class="col-4">
										<ul>
											<li class="none-separator">
												<a href="#add" onClick="fillFields('<?php echo(rawurlencode($agency_type->id)); ?>',
												'<?php echo (rawurlencode($agency_type->type_name)); ?>',
												'<?php echo (rawurlencode($agency_type->short_name)); ?>')">
												<?php echo Kohana::lang('ui_main.edit'); ?>
												</a>
											</li>
										</ul>
									</td>
								</tr>
								<?php endforeach; ?>
								
								</tbody>
							</table>
						</div>
					<?php print form::close(); ?>
				</div>
				
				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					
					<!-- tab -->
					<div class="tab" id="addedit">
						<?php print form::open(NULL,array('id' => 'agencyTypeMain', 'name' => 'agencyTypeMain')); ?>
							<input type="hidden" id="agency_type_id" name="agency_type_id" value="<?php echo $form['agency_type_id']; ?>" />
							<input type="hidden" name="action" id="action" value="a" />
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_huduma.agency_type_name');?></strong><br />
								<?php print form::input('type_name', $form['type_name'], ' class="text long2"'); ?>
							</div>
							<div class="tab_form_item">
								<strong><?php echo Kohana::lang('ui_huduma.short_name');?></strong><br />
								<?php print form::input('short_name', $form['short_name'], ' class="text medium"'); ?>
							</div>

							<div class="tab_form_item">
								&nbsp;<br />
								<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" value="SAVE" />
							</div>
						<?php print form::close(); ?>
					</div>
				</div>
			</div>