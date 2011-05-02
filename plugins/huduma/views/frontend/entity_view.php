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
                <div id="content-section-left">
                    <div id="sidebar-left-content">
						<?php if ($show_dashboard_panel): ?>
						<?php echo $dashboard_panel; ?>
						<?php else: ?>
						    <div class="row">&nbsp;</div>
					    <?php endif; ?>
                    </div>
                </div>

                <div class="report-form">
                    <div class="entity-name">
                        <div class="row">
                            <h1><?php echo $entity_name; ?></h1>
                        </div>
                    </div>
                </div>
                
        		<div id="facebox" style="display: none;">
        			<div class="popup">
        				<div class="content"></div>
        				<a href="#" class="close">
        					<img src="<?php echo url::base().'plugins/huduma/views/images/closelabel.png'; ?>", class="close_image">
        				</a>
        			</div>
        		</div>
                
                <div class="dashboard_container">
                <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
                    <div class="report-form">
                        <div id="report-map" class="entity-map">
                            <div class="map-holder" id="map"></div>
                            <div style="clear:both"></div>
                        </div>
                        
    					<div class="row">
    						<a href="javascript:loadEntityReportForm('<?php echo urlencode($entity_id); ?>')"><?php echo Kohana::lang('ui_main.submit'); ?></a>
    					</div>
    					<?php endif; ?>
                    
                        <?php echo $entity_reports_view; ?>
                        
                        <?php echo $entity_comments_form; ?>

                    </div>
                <?php print form::close(); ?>
                </div>
                
            </div>
