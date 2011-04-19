<?php
/**
 * Change Password view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Home Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">
                <div id="content-section-left">
                    <div id="sidebar-left-content">
						<?php echo $dashboard_panel; ?>
                    </div>
                </div>
                
                <div class="report-form">
                    <div class="entity-name">
                        <div class="row"><h3><?php echo Kohana::lang('ui_huduma.change_password'); ?></h3></div>
                    </div>
                </div>
                

                <div class="dashboard_container">
                    <?php if ($form_error) : ?>
                    <!-- red-box -->
                    <div class="red-box">
                        <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                        <ul>
                         <?php 
                         foreach($errors as $error_item => $description)
                         {
                             print (!$description)? "" : "<li>".$description."</li>";
                         }
                         ?>
                        </ul>
                    </div>
                    <!-- /red-box-->
                    <?php endif; ?>

                    <?php if ($form_saved): ?>
                    <!-- green-box -->
                    <div class="green-box" id="submitStatus">
                        <h3><?php echo Kohana::lang('ui_huduma.profile_updated'); ?></h3>
                    </div>
                    <!-- /green-box-->
                    <?php endif; ?>
                    
                    <?php print form::open(NULL, array('id'=>'changePasswordForm', 'name'=>'changePasswordForm')); ?>
                        <input type="hidden" name="save" id="save" value="" />
                        <input type="hidden" name="action" value="a" />
                    
                        <div class="panel_content">
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.name'); ?></h4>
                                <?php print form::input('name', $form['name'], ' readonly="true" class="text"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.username'); ?></h4>
                                <?php print form::input('username', $form['username'], ' class="text" readonly="true"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.email'); ?></h4>
                                <?php print form::input('email', $form['email'], ' class="text comment_field"'); ?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.password'); ?></h4>
                                <?php print form::password('password', '', ' class="text"')?>
                            </div>
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.password_again'); ?></h4>
                                <?php print form::password('confirm_password', '', ' class="text"');?>
                            </div>
                        </div>
                        <div class="row">
                            <ul class="buttons">
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_main.save'));?></a></li>
                                <li><a href="<?php echo url::site().'dashboards/home'?>" class="btn_cancel"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    <?php print form::close(); ?>
                </div>
            </div>
