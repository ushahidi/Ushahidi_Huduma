<?php 
/**
 * Edit Agency Staff
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Edit Agency Staff View
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('agencies'); ?>
                </h2>

				<?php print form::open(); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />

                    <!-- tabs -->
                    <div class="tabs">
                        <!-- tabset -->
                        <ul class="tabset">
                            <li><a href="<?php echo url::site() ?>admin/agencies"><?php echo Kohana::lang('ui_servicedelivery.view_agencies'); ?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/agencies/edit"><?php echo Kohana::lang('ui_servicedelivery.add_edit_agency');?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/agencies/staff"><?php echo Kohana::lang('ui_servicedelivery.view_staff');?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/agencies/edit_staff" class="active"><?php echo Kohana::lang('ui_servicedelivery.add_edit_staff');?></a></li>
                        </ul>

                        <div class="tab">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.save_staff'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
                                <?php if($agency_staff_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.delete_staff')) ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo url::site().'admin/agencies/staff';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>

                    <?php if ($form_error): ?>
                        <!-- red-box -->
                        <div class="red-box">
                            <h3><?php echo Kohana::lang('ui_main.error');?></h3>
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
                            <h3><?php echo Kohana::lang('ui_servicedelivery.agency_staff_saved'); ?></h3>
                        </div>
                    <?php endif; ?>

                    <div class="report-form">
                        <!-- column -->
                        <div class="sms_holder">
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.full_name');?></h4>
                                <?php print form::input('full_name', $form['full_name'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.email');?></h4>
                                <?php print form::input('email_address', $form['email_address'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.phone_number');?></h4>
                                <?php print form::input('phone_number', $form['phone_number'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_servicedelivery.parent_agency');?></h4>
                                <?php print form::dropdown('agency_id', $agency_array, $form['agency_id']); ?>
                            </div>
                        </div>

                        <div class="simple_border"></div>

                        <div class="btns">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.save_staff'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
                                <?php if($agency_staff_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.delete_staff')) ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo url::site().'admin/agencies/staff';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>
				<?php print form::close(); ?>
                <?php
                    if ($agency_staff_id)
                    {
                        print form::open(url::site().'admin/agencies/staff', array('id'=>'agencyStaffMain', 'name'=>'agencyStaffMain'));
                        print form::hidden(array('action'=>'d', 'agency_staff_id'=>$agency_staff_id));
                        print form::close();
                    }
                ?>
			</div>
