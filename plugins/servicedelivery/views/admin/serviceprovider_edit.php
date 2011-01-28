<?php
/**
 * Service provider edit page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Serviceprovider Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('serviceproviders'); ?>
                </h2>

                <?php print form::open(NULL, array('id'=>'serviceProviderForm', 'name'=>'serviceProviderName')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
                    <!-- tabs -->
                    <div class="tabs">
                        <!-- tabset -->
                        <ul class="tabset">
                            <li><a href="<?php echo url::site() ?>admin/serviceproviders"><?php echo Kohana::lang('ui_main.show_all'); ?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/serviceproviders/edit" class="active"><?php echo Kohana::lang('ui_servicedelivery.add_edit_provider');?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/serviceproviders/officers"><?php echo Kohana::lang('ui_servicedelivery.officers');?></a></li>
                            <li><a href="<?php echo url::site() ?>admin/serviceproviders/edit_officer"><?php echo Kohana::lang('ui_servicedelivery.add_edit_officer');?></a></li>
                        </ul>
                        <!-- /tabset -->

                        <div class="tab">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.save_provider'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
                                <?php if($service_provider_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_main.delete_provider')) ?></a></li>
                                <?php endif; ?>
                                <li><a href="<?php echo url::site().'admin/serviceproviders/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- /tabs -->
                    
                    <!-- service provider form -->
                    <div class="report-form">
                        <?php if ($form_error) : ?>
                            <!-- red-box -->
                            <div class="red-box">
                                <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                                <ul>
                                 <?php 
                                 foreach ($errors as $error_item => $description)
                                 {
                                   print (!$description)? "" : "<li>".$description."</li>";
                                 }
                                 ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($form_saved): ?>
                            <!-- green-box -->
                            <div class="green-box" id="submitStatus">
                                <h3><?php echo Kohana::lang('ui_servicedelivery.provider_saved'); ?></h3>
                            </div>
                        <?php endif; ?>

                        <!-- column -->
                        <div class="f-col">
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_servicedelivery.provider_name'); ?></h4>
                                <?php print form::input('provider_name', $form['provider_name'], ' class="text long2"'); ?>
                            </div>

                            <div class="row">
                                <label>
                                    <span><h4><?php echo Kohana::lang('ui_main.category'); ?></h4></span>
                                    <?php print form::dropdown('category_id', $categories, $form['category_id']); ?>
                                </label>

                                <label>
                                    <span><h4><?php echo Kohana::lang('ui_servicedelivery.service_provider'); ?></h4></span>
                                    <?php print form::dropdown('parent_id', $service_providers, $form['parent_id']); ?>
                                </label>

                                <label>
                                    <span><h4><?php echo Kohana::lang('ui_servicedelivery.boundary'); ?></h4></span>
                                    <?php print form::dropdown('boundary_id', $administrative_boundaries, $form['boundary_id']); ?>
                                </label>
                            </div>

                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_main.description'); ?></h4>
                                <?php print form::textarea('description', $form['description']); ?>
                            </div>
                        </div>
                        <!-- /column -->

                        <div class="simple_border"></div>
                        
                        <div class="btns">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.save_provider'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>

                                <?php if($service_provider_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_servicedelivery.delete_provider')) ?></a></li>
                                <?php endif; ?>

                                <li><a href="<?php echo url::site().'admin/serviceproviders/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>
                <?php print form::close(); ?>
                <?php
                    if ($service_provider_id)
                    {
                        print form::open(url::site().'admin/serviceproviders/', array('id'=>'servceProviderMain', 'name'=>'serviceProviderMain'));
                        print form::hidden(array('action'=>'d', 'service_provider_id[]'=>$service_provider_id));
                        print form::close();
                    }
                ?>
            </div>