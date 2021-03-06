<?php
/**
 * Static entity edit view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('entities'); ?>
                </h2>

                <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
                    <!-- tabs -->
                    <div class="tabs">
                        <!-- tabset -->
                        <ul class="tabset">
                        <li><a href="<?php echo url::site() ?>admin/entities"><?php echo Kohana::lang('ui_main.show_all'); ?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/entities/types"><?php echo Kohana::lang('ui_huduma.entity_types');?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/entities/edit" class="active"><?php echo Kohana::lang('ui_huduma.add_edit_entity');?></a></li>
                        </ul>
                        <!-- /tabset -->

                        <div class="tab">
                            <div class="btns">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_huduma.save_entity'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>

                                <?php if($static_entity_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_huduma.delete_entity')) ?></a></li>
                                <?php endif; ?>

                                <li><a href="<?php echo url::site().'admin/serviceproviders/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                            </div>
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
                                 foreach($errors as $error_item => $description)
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
                                <h3><?php echo Kohana::lang('ui_huduma.entity_saved'); ?></h3>
                            </div>
                        <?php endif; ?>

                        <!-- column -->
                        <div class="f-col-1" style="width:420px;">
                            <div class="row">
                                <h4><?php echo Kohana::lang('ui_huduma.entity_name'); ?></h4>
                                <?php print form::input('entity_name', $form['entity_name'], ' class="text long2"'); ?>
                            </div>
                            <div class="row">
                                <span><h4><?php echo Kohana::lang('ui_huduma.entity_type'); ?></h4></span>
                                <?php print form::dropdown('static_entity_type_id', $entity_types, $form['static_entity_type_id']); ?>
                            </div>
                            <div class="row">
                                <span><h4><?php echo Kohana::lang('ui_huduma.parent_agency'); ?></h4></span>
                                <?php print form::dropdown('agency_id', $agencies, $form['agency_id']); ?>
                            </div>
                            <div class="row">
                                <span><h4><?php echo Kohana::lang('ui_huduma.boundary'); ?></h4></span>
                                <?php print form::dropdown('boundary_id', $boundaries, $form['boundary_id']); ?>
                            </div>
                            
                            <?php 
                            if ($static_entity_id)
                            {
                                // Show the metadata block only when a static entity exists
                                 echo $entity_metadata;
                            }
                            ?>
							
                        </div>
                        <!-- /column -->

                        <!-- f-col -->
                        <div class="f-col" style="width: 405px; margin-left: 60px;">
                            <div class="incident-location">
                                <h4><?php echo Kohana::lang('ui_huduma.entity_location'); ?></h4>
                                <div class="location-info">
                                    <span><?php echo Kohana::lang('ui_main.latitude'); ?>:</span>
                                    <?php print form::input('latitude', $form['latitude'], ' class="text"'); ?>
                                    <span><?php echo Kohana::lang('ui_main.longitude'); ?>:</span>
                                    <?php print form::input('longitude', $form['longitude'], ' class="text"'); ?>
                                </div>
                                <div style="clear:both"></div>
                                <div id="divMap" class="entity_map_holder" style="width:395px; height: 300px;"></div>
                            </div>
                        </div>
                        <!-- /f-col-1 -->

                        <div class="simple_border"></div>
                        
                        <div class="btns">
                            <ul>
                                <li><a href="#" class="btn_save"><?php echo strtoupper(Kohana::lang('ui_huduma.save_entity'));?></a></li>
                                <li><a href="#" class="btn_save_close"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>

                                <?php if($static_entity_id): ?>
                                    <li><a href="#" class="btn_delete btns_red"><?php echo strtoupper(Kohana::lang('ui_huduma.delete_entity')) ?></a></li>
                                <?php endif; ?>

                                <li><a href="<?php echo url::site().'admin/serviceproviders/';?>" class="btns_red"><?php echo strtoupper(Kohana::lang('ui_main.cancel'));?></a></li>
                            </ul>
                        </div>
                    </div>
                <?php print form::close(); ?>
                <?php
                    if ($static_entity_id)
                    {
                        print form::open(url::site().'admin/entities', array('id'=>'entityMain', 'name'=>'entityMain'));
                        print form::hidden(array('action'=>'d', 'static_entity_id[]'=>$static_entity_id));
                        print form::close();
                    }
                ?>
            </div>