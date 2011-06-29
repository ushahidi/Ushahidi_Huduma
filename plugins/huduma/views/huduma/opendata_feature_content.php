<div class="opendata-analytics">
	<h4><?php echo $data_title.': '.$boundary_name; ?></h4>
	<table class="breakdown_table">
	<?php foreach ($feature_data as $key => $value): ?>
		<tr>
			<td class="header"><?php echo $key; ?></td>
			<td><?php echo $value; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>