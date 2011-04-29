<?php
/**
 * Static entity metadata view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
    <!-- metadata -->
	<div class="row">
		<h4>
			<?php echo Kohana::lang('ui_huduma.additional_info'); ?>
			[<a href="javascript:showAddMetadataDialog(<?php echo urlencode($static_entity_id); ?>)">
				<?php echo Kohana::lang('ui_huduma.add_info'); ?>
			</a>]
		</h4>
		
		<div id="facebox" style="display: none;">
			<div class="popup">
				<div class="content"></div>
				<a href="#" class="close">
					<img src="<?php echo url::base().'plugins/huduma/views/images/closelabel.png'; ?>", class="close_image">
				</a>
			</div>
		</div>

		<?php if ($metadata_items->count() > 0): ?>
		<div class="metadata-box" style="border: 1px dashed #CCCCCC; padding:3px;">
			<table class="metadata" id="metadata-list" width="100%">
				<thead>
					<tr>
						<th><?php echo Kohana::lang('ui_huduma.item_label'); ?></th>
						<th><?php echo Kohana::lang('ui_huduma.value'); ?></th>
						<th><?php echo Kohana::lang('ui_huduma.as_of_year'); ?></th>
						<th width="20%"><?php echo Kohana::lang('ui_admin.actions'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($metadata_items as $item): ?>
					<tr id="metadata_row_<?php echo $item->id; ?>">
						<td class="edit_item" id="item_label_<?php echo $item->id; ?>"><?php echo $item->item_label; ?></td>
						<td class="edit_item" id="item_value_<?php echo $item->id; ?>"><?php echo $item->item_value; ?></td>
						<td class="edit_item" id="as_of_year_<?php echo $item->id; ?>"><?php echo $item->as_of_year; ?></td>
						<td nowrap="nowrap">
						    <ul class="huduma-actions">
    						    <li>
    						        <a href="#" id="edit_link_<?php echo $item->id; ?>" onclick="metadataItemAction('e', '<?php echo $item->id; ?>', this)">
        						        <?php echo Kohana::lang('ui_main.edit');?>
    						        </a>
    						    </li>
    						    <li>
        						    <a href="#" id="delete_link_<?php echo $item->id; ?>" onclick="metadataItemAction('d', '<?php echo $item->id; ?>', this)">
            						    <?php echo Kohana::lang('ui_main.delete');?>
        						    </a>
    						    </li>
    						    <li>
        						    <a href="#" style="display:none" id="save_link_<?php echo $item->id; ?>" 
        						        onclick="metadataItemAction('s', '<?php echo urlencode($item->id); ?>', this)">
            						    <?php echo Kohana::lang('ui_main.save');?>
        						    </a>
    						    <li>
    						    <li>
        						    <a href="#" style="display:none;" id="cancel_link_<?php echo $item->id; ?>" 
        						        onclick="metadataItemAction('c', '<?php echo urlencode($item->id); ?>', this)">
            						    <?php echo Kohana::lang('ui_main.cancel');?>
        						    </a>
    						    <li>
						    </ul>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>

	</div>
    <!-- /metadata -->