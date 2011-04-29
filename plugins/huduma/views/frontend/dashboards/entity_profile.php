<?php
/**
 * View page for displaying the entity profile
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
            <div class="row">
                <h1><?php echo Kohana::lang('ui_huduma.entity_profile'); ?></h1>
            </div>
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
        <?php endif; ?>

        <?php if ($form_saved): ?>
            <!-- green-box -->
            <div class="green-box" id="submitStatus">
                <h3><?php echo Kohana::lang('ui_huduma.profile_updated'); ?></h3>
            </div>
        <?php endif; ?>
        
    <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
        <input type="hidden" name="save" id="save" value="" />
        <input type="hidden" name="action" value="a" />

        <div class="panel_content">
            <div class="left-col">
                <div class="row">
                    <h4><?php echo Kohana::lang('ui_huduma.entity_name');?></h4>
                    <?php print form::input('entity_name', $form['entity_name'], ' class="text medium"'); ?>
                </div>
                <div class="row">
                    <h4><?php echo Kohana::lang('ui_huduma.entity_type'); ?></h4>
                    <?php print form::dropdown('static_entity_type_id', $entity_types_dropdown, $form['static_entity_type_id']); ?>
                </div>
                <div class="row">
                    <h4><?php echo Kohana::lang('ui_huduma.agency'); ?></h4>
                    <?php print form::dropdown('agency_id', $agencies_dropdown, $form['agency_id']); ?>
                </div>
                <div class="row">
                    <h4><?php echo Kohana::lang('ui_huduma.boundary'); ?></h4>
                    <?php print form::dropdown('boundary_id', $boundaries_dropdown, $form['boundary_id']); ?>
                </div>
            </div>
            <div class="right-col">
                <div class="location-info">
                    <span><?php echo Kohana::lang('ui_main.latitude'); ?>:</span>
                    <?php print form::input('latitude', $form['latitude'], ' class="text"'); ?>
                    <span><?php echo Kohana::lang('ui_main.longitude'); ?>:</span>
                    <?php print form::input('longitude', $form['longitude'], ' class="text"'); ?>
                </div>
                <div id="divMap" style="width: 350px; height: 300px;"></div>
            </div>
            <?php echo $entity_metadata; ?>
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
