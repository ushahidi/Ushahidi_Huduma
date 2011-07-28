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
				<div class="dashboard-title">
					<h1><?php echo $entity_name; ?></h1>
				</div>
				<div style="clear: both;"></div>
				<div id="facebox" style="display: none;">
					<div class="popup">
						<div class="content"></div>
						<a href="#" class="close">
							<img src="<?php echo url::base().'plugins/huduma/views/images/closelabel.png'; ?>", class="close_image">
						</a>
					</div>
				</div>
				
				<div id="pageColLeft">
					<div id="content-section-left">
						<div id="sidebar-left-content">
							<div style="float: right;">
								<input type="button" class="btn_submit btn_submit2" value="<?php echo Kohana::lang('ui_main.submit'); ?>" 
									onclick="loadEntityReportForm('<?php echo urlencode($entity_id); ?>')">
							</div>
							<div style="clear: both;"></div>
							<?php if ($show_dashboard_panel): ?>
								<?php echo $dashboard_panel; ?>
							<?php else: ?>
								<div class="row">&nbsp;</div>
							<?php endif; ?>
							<div style="clear: both;"></div>
							<?php if ($neighbour_facilities): ?>
							<div class="report-additional-reports">
								<h4><?php echo strtoupper(Kohana::lang('ui_huduma.neighbour_facilities')); ?></h4>
								<?php foreach($neighbour_facilities as $neighbour): ?>
									<div class="rb_report">
										<h5>
											<a href="<?php echo url::site().'entities/view/'.$neighbour->id; ?>">
												<?php echo preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($neighbour->entity_name)); ?>
											</a>
										</h5>
										<p class="r_location"><?php echo $neighbour->type_name." - ".round($neighbour->distance, 2); ?> Kms</p>
									</div>
								<?php endforeach; ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				
				<div id="pageColRight">
					<div class="dashboard_container">
						<?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
							<input type="hidden" name="save" id="save" value="" />
							<input type="hidden" name="action" value="a" />
							<?php print form::input(array('type'=>'hidden', 'name'=>'entity_id', 'id'=>'entity_id'), $entity_id); ?>
                    
							<div class="single-entity-map">
								<div style="clear: both;"></div>
								<div id="map" style="width: 575px; height: 205px;"></div>
							</div>
						
							<!-- static entity metadata -->
							<div class="entity-additional-info" style="display:none">
								<div id="metadataPageContent" style="margin:0; padding: 0">	
									<?php echo $entity_metadata_view; ?>
								</div>
							</div>
							<!-- /static entity metadata -->
						
							<div class="report-filter-tabs">
								<ul>
									<li><a id="filterAll" href="javascript:loadReportItems('filterAll', '<?php echo $fetch_url;?>', 'all')" class="active">
										<?php echo Kohana::lang('ui_huduma.all_reports')?></a></li>
									<li><a id="filterResolved", href="javascript:loadReportItems('filterResolved', '<?php echo $fetch_url;?>', 'resolved')">
										<?php echo Kohana::lang('ui_huduma.resolved')?></a></li>
									<li><a id="filterUnresolved" href="javascript:loadReportItems('filterUnresolved', '<?php echo $fetch_url;?>', 'unresolved')">
										<?php echo Kohana::lang('ui_huduma.unresolved'); ?></a></li>
								</ul>
							</div>
							
							<div id="emptyFilterResults" style="display:none">
								<p><?php echo Kohana::lang('ui_huduma.no_reports_found'); ?></p>
							</div>
							
							<div class="reports-list-holder">
								<?php echo $entity_reports_view; ?>
							</div?
							
						<?php print form::close(); ?>
					</div>
				</div>
			</div>
		</div>