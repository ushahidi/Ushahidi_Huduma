<?php
/**
 * View page for adding static entity metadata
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
        <h2 class="dialog-title"><?php echo strtoupper(Kohana::lang('ui_huduma.add_info')); ?></h2>
        <?php print form::open(); ?>
            <?php print form::hidden('entity_id', $static_entity_id); ?>
            <div class="row">
                <a href="javascript:addMetadataItem(<?php echo urlencode($static_entity_id); ?>)">
        			<?php echo Kohana::lang('ui_huduma.add_info'); ?>
        		</a>
    		</div>
    		
    		<div id="metadata_item_new"></div>
    		
			<div class="btns">
				<ul>
					<li><a href="#" id="save_metadata"><?php echo strtoupper(Kohana::lang('ui_main.save_close'));?></a></li>
					<li>
					    <a class="btns_red" href="<?php url::site().'admin/entities/edit/'.$static_entity_id?>">
					        <?php echo strtoupper(Kohana::lang('ui_main.cancel'));?>
					    </a>
					</li>
				</ul>
			</div>
			
		<?php print form::close(); ?>
		