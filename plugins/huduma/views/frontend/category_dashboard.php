<?php
/**
 * View page for the category dashboards
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboards Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		<?php 
			$base_image_url = url::base().'plugins/huduma/views/images/'; 
			$gradient_map = navigator::get_color_gradient_map($category->category_color);
		?>
		<div class="bg">
			<div id="content-section-left">
				<!-- dashboard menu -->
				<div id="sidebar-left-content">
					<div class="nav_dashboard_panel">
						<ul class="dashboard_menu">
							<li>
								<a href="#">
									<?php echo Kohana::lang('ui_huduma.dashboard_home'); ?>
									<img src="<?php echo $base_image_url.'homeicon.png'?>" border="0">
								</a>
							</li>
							<li><a href="#"><?php echo Kohana::lang('ui_huduma.report_analysis'); ?></li>
							<li><a href="#"><?php echo Kohana::lang('ui_huduma.report_locations')?></a></li>
						</ul>
					</div>
				</div>
				<!-- /dashboard menu -->
			</div>
			
			<div class="dashboard-title">
				<h1 style="color: #<?php echo $category->category_color; ?>">
					<?php echo ucfirst(strtolower($category->category_title)).' '.Kohana::lang('ui_huduma.dashboard'); ?>
				</h1>
			</div>
			
			<div class="dashboard_container">
				<div class="snapshot-item">
					<div class="snapshot-item-header">
						<div class="dash-stats">
							<div class="cat-report-totals">
								<img src="<?php echo $base_image_url.'graph.png'; ?>" align="left">
								<div class="cat-report-count" style="color: #<?php echo $category->category_color; ?>;">
									<?php echo $category_stats['total_reports']; ?>
								</div>
								<div>
								<p class="count-footer" style="float:right; font-size:11px; color: #ADADAD; font-weight:bold; width:67%; padding:0; margin:0;">
									<?php echo strtoupper(Kohana::lang('ui_huduma.total').' '.($category->category_title).' '.Kohana::lang('ui_main.reports')); ?>
								</p>
								</div>
							</div>
							<div class="cat-response-graphs">
								<dl class="cat-stats-graph">
								<?php if ($category_stats['unassigned'] > 0): ?>
									<dt>%<?php echo Kohana::lang('ui_huduma.unassigned_reports');?></dt>
									<dd style="background-color: #<?php echo $gradient_map[0]?>">
										<?php echo round(($category_stats['unassigned']/$category_stats['total_reports'])*100,2);?>
									</dd>
								<?php endif; ?>
									<dt>%<?php echo Kohana::lang('ui_huduma.unresolved'); ?></dt>
									<dd style="background-color: #<?php echo $gradient_map[11]; ?>;">
										<?php echo $category_stats['unresolved']; ?>
									</dd>
									<dt>%<?php echo Kohana::lang('ui_huduma.resolved'); ?></dt>
									<dd style="background-color: #<?php echo $gradient_map[5]; ?>">
										<?php echo $category_stats['resolved']; ?>
									</dd>
								</dl>
							</div>
						</div>
					</div>
				</div>
				<div style="clear:both"></div>
				<div class="report-filter-tabs">
					<?php $fetch_url  = url::site().'dashboards/?action=filter&type=category&id='.$category->id; ?>
					<ul>
						<li><a id="filterAll" href="javascript:loadReportItems('filterAll', '<?php echo $fetch_url;?>', 'all')" class="active"><?php echo Kohana::lang('ui_huduma.all_reports')?></a></li>
						<li><a id="filterResolved", href="javascript:loadReportItems('filterResolved', '<?php echo $fetch_url;?>', 'resolved')"><?php echo Kohana::lang('ui_huduma.resolved')?></a></li>
						<li><a id="filterUnresolved" href="javascript:loadReportItems('filterUnresolved', '<?php echo $fetch_url;?>', 'unresolved')"><?php echo Kohana::lang('ui_huduma.unresolved'); ?></a></li>
					</ul>
				</div>
				<div id="emptyFilterResults" style="display:none">
					<p><?php echo Kohana::lang('ui_huduma.no_reports_found'); ?></p>
				</div>
				<?php echo $category_reports_view; ?>
			</div>
		</div>