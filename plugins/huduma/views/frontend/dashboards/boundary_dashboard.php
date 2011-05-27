<?php
/**
 * Landing page for the boundary-level dashboards
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Huduma
 * @module     Dashboard Home Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		<div class="bg">
			<div class="dashboard-title">
				<h1><?php echo $boundary_name; ?></h1>
				<div class="top-content-filters">
					<ul>
						<li>
							<a href="javascript:toggleItemDisplay('dashboard-map', 'dash-category-panel')">
								<?php echo Kohana::lang('ui_huduma.view_map'); ?>
							</a>
						</li>
						<li>
							<a href="javascript:toggleItemDisplay('dash-category-panel', 'dashboard-map')">
								<?php echo Kohana::lang('ui_main.all_categories'); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div style="clear: both"></div>

			<div id="pageColLeft">
				<div id="content-section-left">
					<div id="sidebar-left-content">
						<?php echo $dashboard_panel; ?>
					</div>
				</div>
			</div>

			<div id="pageColRight">
				<div class="dashboard_container">
					<div class="dash-category-panel" style="display:none;">
						<!-- category filters -->
						<?php if ($categories): ?>
						<ul class="dash-cat-filter">
							<?php foreach ($categories as $key => $value): ?>
							<li><a href="#" id="category_<?php echo $key; ?>"><?php echo $value; ?></a></li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
						<!-- /category filters -->
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<th>
									<?php echo $total_reports; ?>
									<div><span><?php echo strtoupper(Kohana::lang('ui_main.reports')); ?><span></div>
								</th>
								<td>
									<dl class="dash_bar_graphs">
										<dt>% <?php echo Kohana::lang('ui_huduma.unresolved'); ?></dt><dd><?php echo $total_unresolved; ?></dd>
										<dt>% <?php echo Kohana::lang('ui_huduma.resolved'); ?></dt><dd><?php echo $total_resolved; ?></dd>
									</dl>
								</td>
						</table>
					</div>
					<div id="divMap" class="dashboard-map"></div>
					<div style="clear: both;"></div>
				
					<div class="row">
						<?php echo $boundary_reports_view; ?>
					</div>
				</div>
			</div>
			
		</div>