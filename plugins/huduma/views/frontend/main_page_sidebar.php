<?php
/**
 * View page for the content on the left side of the main page
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - https://github.com/ushahidi/Ushahidi_Web
 * @module     Huduma Plugin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		<div class="stats-overview">
			<div id="pieChartHolder">
				<div class="stat-overview-right">
					<div class="response-rate">
						<p>
							<?php echo $stats['unassigned']; ?><span>%</span>
						</p>
						<div style="clear: both;"></div>
						<h3><?php echo strtoupper(Kohana::lang('ui_huduma.unassigned_reports')); ?></h3>
					</div>
					<div class="total-reports">
						<p><?php echo $stats['total_reports']; ?></p>
					</div>
				</div>
				<div id="resolvedStats">
					<p><?php echo $stats['resolved']; ?></p>
					<span>%</span>
				</div>
				<div id="unresolvedStats">
					<p><?php echo $stats['unresolved']; ?></p>
					<span>%</span>
				</div>
				<?php echo $main_sidebar_js; ?>
				<div id="pieChartSummary"></div>
			</div>
		</div>