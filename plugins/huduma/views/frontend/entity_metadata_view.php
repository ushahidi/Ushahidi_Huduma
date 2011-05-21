<?php
/**
 * View file for static entity metadata
 * This view is meant to be embedded within a parent view
 */
?>
	<?php if ($metadata_pagination->total_items > 0): ?>
		<div style="float: left; padding: 5px 0;">
			<h6><?php echo Kohana::lang('ui_huduma.additional_info'); ?></h6>
		</div>
		<div class="metadata-pager" style="float: right; padding: 5px 0;">
			<?php echo $metadata_pagination ?>
		</div>
		<div style"clear: both;"></div>
		<table>
			<tr style="background-color: #<?php echo $entity->static_entity_type->category->category_color; ?>">
				<th align="left"><?php echo Kohana::lang('ui_huduma.item_label'); ?></th>
				<th align="right"><?php echo Kohana::lang('ui_huduma.value'); ?></th>
			</tr>
			<?php foreach ($metadata as $metadata_item): ?>
			<tr class="metadata-item">
				<td><?php echo $metadata_item->item_label; ?></td>
				<td align="right"><?php echo $metadata_item->item_value; ?></td>
			</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
