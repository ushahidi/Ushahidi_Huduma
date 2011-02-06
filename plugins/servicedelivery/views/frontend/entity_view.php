<?php
/**
 * Single Entity view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Entities Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">

                <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
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
                                <h3><?php echo Kohana::lang('ui_servicedelivery.entity_saved'); ?></h3>
                            </div>
                        <?php endif; ?>

                        <!-- column -->
                        <div class="f-col">
                            <div class="row">
                                <h1><?php echo $entity_name; ?></h1>
                            <!-- metadata -->
                            <!-- /metadata -->
                        </div>
                        <!-- /column -->

                        <!-- f-col-1 -->
                        <div class="f-col-1">
                            <div class="incident-location">
                                <div class="location-info">
                                    <span><?php echo $latitude ?>:</span>
                                    <span><?php echo $longitude; ?>:</span>
                                </div>
                                <div style="clear:both"></div>
                                <div id="divMap" class="map_holder_reports"></div>
                            </div>
                        </div>
                        <!-- /f-col-1 -->

                        <div class="simple_border"></div>
                        
                    </div>
                <?php print form::close(); ?>
                <?php
                    if ($entity_id)
                    {
                        print form::open(url::site().'admin/entities', array('id'=>'entityMain', 'name'=>'entityMain'));
                        print form::hidden(array('action'=>'d', 'static_entity_id[]'=>$entity_id));
                        print form::close();
                    }

                ?>
            </div>
