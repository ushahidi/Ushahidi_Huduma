<?php
/**
 * Dashboard users view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard users Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
 ?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('dashboard_users'); ?>
                </h2>

                <!-- tabs -->
                <div class="tabs">
                    <!-- tabset -->
                    <ul class="tabset">
                        <li><a href="<?php echo url::site() ?>admin/dashboard/users" class="active"><?php echo Kohana::lang('ui_huduma.dashboard_users');?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/dashboard/users/edit"><?php echo Kohana::lang('ui_admin.manage_users_edit');?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/dashboard/roles"><?php echo Kohana::lang('ui_huduma.dashboard_roles');?></a></li>
                    </ul>
                    <!-- /tabset -->

            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="dashboardUserAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="dashboardUserAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
            			</ul>
            		</div>
                </div>
                <!-- /tabs -->

            	<?php if ($form_error) : ?>
            		<!-- red-box -->
            		<div class="red-box">
            			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
            			<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
            		</div>
            	<?php endif; ?>

                <?php if ($form_saved): ?>
            		<!-- green-box -->
            		<div class="green-box" id="submitStatus">
            			<h3><?php echo Kohana::lang('ui_huduma.dashboard_user_saved');?> <?php echo $form_action; ?>
            			    <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a>
            			</h3>
            		</div>
            	<?php endif; ?>
                
            	<!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL, array('id' => 'dashboardUser', 'name' => 'dashboardUser')); ?>
                        <input type="hidden" name="action" id="action" value="" />
                        <input type="hidden" name="dashboard_user_id[]" id="dashboard_user_single" value="" />

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                        <input id="checkAllUsers" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'dashboard_user_id[]' )" /></th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_admin.header_user');?></th>
                                        <th class="col-4"><?php echo Kohana::lang('ui_admin.actions');?></th>
                                    </tr>
                                </thead>

                                <!-- table footer -->
                                <tfoot>
                                    <tr class="foot">
                                        <td colspan="4">
                                            <?php echo $pagination; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                                <!-- /table footer -->

                            <tbody>
                                <?php if ($total_items == 0): ?>
                                    <tr>
                                        <td colspan="4" class="col">
                                            <h3><?php echo Kohana::lang('ui_admin.no_result_display_msg');?></h3>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php
                                    foreach ($dashboard_users as $user)
                                    {
										$dashboard_user_id = $user->id;
										$name = $user->name;
                                        $username = $user->username;
										$email = $user->email;
                                 ?>
                                        <tr>
                                            <td class="col-1">
                                                <input name="agency_staff_id[]" id="agency" value="<?php echo $dashboard_user_id; ?>" type="checkbox" class="check-box"/>
                                            </td>

                                            <td class="col-2">
												<div class="post">
													<h4><a href="<?php echo url::site().'/admin/dashboard/users/edit/'.$dashboard_user_id; ?>"><?php echo $name." (".$username.")"; ?></a></h4>
												</div>
												<ul class="info">
													<li class="none-separator">
														<?php echo Kohana::lang('ui_main.email').": "; ?>
														<strong><?php echo $email; ?></strong>
													</li>
												</ul>
											</td>

                                            <td class="col-4">

                                                <!-- actions -->
                                                <ul>
                                                    <li class="none-separator">
                                                        <a href="<?php echo url::site().'admin/dashboard/users/edit/'.$dashboard_user_id; ?>">
                                                            <?php echo Kohana::lang('ui_admin.edit_action'); ?>
                                                        </a>
                                                    </li>
                                                    <li class="none-separator">
                                                        <a href="#" class="del" onclick="dashboardUserAction('d','DELETE', '<?php echo $dashboard_user_id; ?>');">
                                                            <?php echo Kohana::lang('ui_admin.delete_action') ;?>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <!-- /actions -->

                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php print form::close(); ?>
                </div>
            </div>