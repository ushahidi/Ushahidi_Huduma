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

				<div class="dashboard-title">
					<h1><?php echo $entity_name; ?></h1>
					<div class="top-content-filters">
						<ul>
							<li><a href="#" id="viewMapLink" class="active"><?php echo Kohana::lang('ui_huduma.view_map'); ?></a><li>
							<li><a href="#" id="viewMetadataLink"><?php echo Kohana::lang('ui_huduma.view_additional_info'); ?><li></li>
						</ul>
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
						<?php print form::input(array('type'=>'hidden', 'name'=>'entity_id', 'id'=>'entity_id'), $entity_id); ?>
                    
						<div class="single-entity-map">
							<div id="map" style="width: 650px; height: 205px;"></div>
						</div>
						<div style="clear:both"></div>
						
						<!-- static entity metadata -->
						<div class="entity-additional-info" style="display:none">
							<div id="metadataPageContent" style="margin:0; padding: 0">	
								<?php echo $entity_metadata_view; ?>
							</div>
						</div>
						<!-- /static entity metadata -->
						
						<?php
						/*
						<div class="row">
							<a href="javascript:loadEntityReportForm('<?php echo urlencode($entity_id); ?>')"><?php echo Kohana::lang('ui_main.submit'); ?></a>
						</div>
						*/
						?>
						<?php echo $entity_reports_view; ?>
							
					<?php print form::close(); ?>
				</div>
			</div>
