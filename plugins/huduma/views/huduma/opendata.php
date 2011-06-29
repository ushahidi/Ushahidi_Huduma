<?php
/**
 * View file for the opendata controller
 */
?>
	<div id="content">
		<div class="content-bg">
			<div class="page-title"><h1><?php echo Kohana::lang('opendata.opendata');?> </h1></div>
			<div style="clear:both;"></div>
			<div style="float: left; width: 350px;">
				<div id="featureContent">
					<div class="opendata-analytics ">
						<ul class="analytics-menu">
							<li>
								<a href="#"><?php echo Kohana::lang('opendata.overlays'); ?></a>
								<div>
									<table>
										<tr>
											<td><?php echo Kohana::lang('opendata.category_sector'); ?></td>
											<td><?php print form::dropdown(array('name' => 'category_id', 'id' => 'category_id', 'style' => 'width: 150px;'), $categories); ?></td>
										</tr>
										<tr>
											<td><?php echo Kohana::lang('opendata.select_facility'); ?></td>
											<td><?php print form::dropdown(array('name' => 'facility_type', 'id' => 'facility_type', 'style' => 'width: 250px;'), NULL); ?></td>
										</tr>
										<tr>
											<td colspan="2"><input type="button" name="apply" id="apply_overlay" value="Apply"></td>
										</tr>
									</table>
								</div>
							</li>
							<li>
								<a href="#" class="active"><?php echo Kohana::lang('opendata.overview'); ?></a>
								<div>
									<h4>Total Population: <?php echo $total; ?> </h4>
									<?php foreach ($breakdown_data as $key => $value): ?>
										<dl class="breakdown-stats">
											<dt><?php echo $key?></dt><dd style="background-color:#<?php echo $value['color']; ?>;"><?php echo $value['total']; ?></dd>
										</dl>
									<?php endforeach; ?>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div style="float: right;">
				<div id="opendataMap" class="opendata-map" style="width:720px; height: 550px; border:1px solid #CCCCCC;">
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>