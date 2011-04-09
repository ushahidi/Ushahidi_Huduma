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
                    <?php navigator::subtabs('dashboard_users'); ?>
                </h2>

				<?php print form::open(NULL, array('name'=>'dashoardUserForm', 'id'=>'dashboardUserForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />

                    <!-- tabs -->
                    <div class="tabs">
                        <!-- tabset -->
                        <ul class="tabset">
                            <li><a href="<?php echo url::site() ?>admin/dashboard/users"><?php echo Kohana::lang('ui_huduma.dashboard_users');?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/dashboard/users/edit" class="active"><?php echo Kohana::lang('ui_admin.manage_users_edit');?></a></li>
							<li><a href="<?php echo url::site() ?>admin/dashboard/roles"><?php echo Kohana::lang('ui_huduma.dashboard_roles');?></a></li>
                        </ul>

                        <div class="tab">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_huduma.save_user'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
                                <?php if ($dashboard_user_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_huduma.delete_user')) ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo url::site().'admin/dashboard/users';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
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
                            <h3><?php echo Kohana::lang('ui_huduma.dashboard_user_saved'); ?></h3>
                        </div>
                    <?php endif; ?>

                    <div class="report-form">
                        <!-- column -->
                        <div class="sms_holder">
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.full_name');?></h4>
                                <?php print form::input('name', $form['name'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.username');?></h4>
                                <?php print form::input('username', $form['username'], ' class="text"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.password');?></h4>
                                <?php print form::password('password', '', ' class="text"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.password_again');?></h4>
                                <?php print form::password('confirm_password', '', ' class="text"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.email');?></h4>
                                <?php print form::input('email', $form['email'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_huduma.dashboard_role');?></h4>
                                <?php print form::dropdown('dashboard_role_id', $dashboard_roles, $form['dashboard_role_id']); ?>
                            </div>
                            <div class="row">
                                <h4>
									<?php print form::checkbox('is_active', $form['is_active'], $form['is_active']); ?>
									<?php echo Kohana::lang('ui_huduma.is_active');?>
								</h4>
                            </div>
                        </div>

                        <div class="simple_border"></div>

                        <div class="btns">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_huduma.save_user'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
                                <?php if ($dashboard_user_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_huduma.delete_user')) ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo url::site().'admin/dashboard/users';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>
				<?php print form::close(); ?>
                <?php
                    if ($dashboard_user_id)
                    {
                        print form::open(url::site().'admin/dashboard/users', array('id'=>'dashboardUsersMain', 'name'=>'dashboardUsersMain'));
                        print form::hidden(array('action'=>'d', 'dashboard_user_id'=>$dashboard_user_id));
                        print form::close();
                    }
                ?>
			</div>
