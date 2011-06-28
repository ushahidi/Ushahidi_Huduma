<?php
/**
 * Displays a snapshot of the active catgeroy dashboards
 */
?>
		<div class="dash-snapshots">
		<?php $base_image_url = url::base().'plugins/huduma/views/images/'; ?>
		<?php foreach ($category_snapshots as $category): ?>
			<?php
				// Get the actual numbers
				$total_reports = $category->total_reports;
				$unresolved = $category->unresolved;
				$resolved = $category->resolved;
				$unassigned = $total_reports - ($unresolved + $resolved);
				
				// Convert to percentages
				$unresolved = ($unresolved > 0)? round(($unresolved/$total_reports) * 100,2) : 0;
				$resolved = ($resolved > 0)? round(($resolved/$total_reports)*100,2) : 0;
				
				// Dashboard Acess URL
				$dashboard_url = url::site().'dashboards/?action=list&type=category&id=%d';
				
				// Get the color gradient
				$gradient_map = navigator::get_color_gradient_map($category->category_color);
			?>
			<div class="snapshot-item">
				<div class="snap-item-header">
					<img src="<?php echo $base_image_url.'arrow2.png'; ?>" width="18" height="24" align="left">
					<h1><?php echo ucfirst(strtolower($category->category_title)); ?></h1>
					<div class="dash-stats">
						<div class="cat-report-totals">
							<img src="<?php echo $base_image_url.'graph.png'; ?>" align="left">
							<div class="cat-report-count" style="color: #<?php echo $category->category_color; ?>;">
								<?php echo $total_reports; ?>
							</div>
							<div>
							<p class="count-footer" style="float:right; font-size:11px; color: #ADADAD; font-weight:bold; width:67%; padding:0; margin:0;">
								<?php echo strtoupper(Kohana::lang('ui_huduma.total').' '.($category->category_title).' '.Kohana::lang('ui_main.reports')); ?>
							</p>
							</div>
						</div>
						<div class="cat-response-graphs">
							<dl class="cat-stats-graph">
							<?php if ($unassigned > 0): ?>
								<dt>%<?php echo Kohana::lang('ui_huduma.unassigned_reports');?></dt><dd style="background-color: #<?php echo $gradient_map[0]?>"><?php echo round(($unassigned/$total_reports)*100,2);?></dd>
							<?php endif; ?>
								<dt>%<?php echo Kohana::lang('ui_huduma.unresolved'); ?></dt><dd style="background-color: #<?php echo $gradient_map[11]; ?>;"><?php echo $unresolved; ?></dd>
								<dt>%<?php echo Kohana::lang('ui_huduma.resolved'); ?></dt><dd style="background-color: #<?php echo $gradient_map[5]; ?>"><?php echo $resolved; ?></dd>
							</dl>
						</div>
					</div>
					<a href="<?php echo sprintf($dashboard_url, $category->id); ?>">
					<?php echo Kohana::lang('ui_huduma.click_to_view_reports').' '.strtolower($category->category_title); ?>
					</a>
				</div>
			</div>
			<div style="clear: both;"></div>
		<?php endforeach; ?>
		</div>