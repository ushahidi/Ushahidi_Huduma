<?php
/**
 * Boundary view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs("boundaries"); ?>
                </h2>

                <div class="tabs">
                    <!-- tabset -->
                    <ul class="tabset">
                        <li><a href="<?php echo url::site() ?>admin/servicedelivery" class="active"><?php echo Kohana::lang('ui_main.show_all'); ?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/servicedelivery/types"><?php echo Kohana::lang('ui_servicedelivery.boundary_types');?></a></li>
                    </ul>
                    <!-- /tabset -->

            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="boundaryAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="boundaryAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
            			</ul>
            		</div>
                </div>

                <?php if ($form_error): ?>
                <!-- red-box -->
                <div class="red-box">
                    <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                    <ul>
                    <?php
                    foreach ($errors as $error_item => $error_description)
                    {
                        print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
                    }
                    ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if ($form_saved): ?>
                    <!-- green-box -->
                    <div class="green-box">
                        <h3><?php echo Kohana::lang('ui_servicedelivery.boundary');?> <?php echo $form_action; ?></h3>
                    </div>
                <?php endif; ?>
                
                <!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL,array('id' => 'boundaryListing','name' => 'boundaryListing')); ?>
                        <input type="hidden" name="action" id="action" value="">
                        <input type="hidden" name="boundary_id[]" id="boundary_id_single" value="">

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                            <input type="checkbox" id="checkAllBoundaries" class="check-box" onclick="CheckAll(this.id, 'boundary_id[]')"
                                        </th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_servicedelivery.boundary');?></th>
                                        <th class="col-4"><?php echo Kohana::lang('ui_admin.actions');?></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr class="foot">
                                        <td colspan="3"><?php echo $pagination; ?></td>
                                    </tr>
                                </tfoot>
                                <tbody>
                                <?php if ($total_items == 0): ?>
                                    <tr>
                                        <td colspan="4" class="col">
                                        <h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php
                                foreach ($top_level_boundaries as $boundary)
                                {
                                    $boundary_id = $boundary->id;
                                    $boundary_type_id = $boundary->boundary_type_id;
                                    $boundary_name = strtoupper($boundary->boundary_name." ".$boundary->boundary_type->boundary_type_name);
                                    $parent_id = $boundary->parent_id;
                                    $parent_css_id = 'boundary_'.$boundary_id;
                                ?>
                                    <tr>
                                        <td class="col-1">
                                            <input type="checkbox" class="check-box" id="boundary" name="boundary_id" value="<?php echo $boundary_id?>" />
                                        </td>
                                        <td class="col-2">
                                            <span id="arrow_<?php echo $boundary_id; ?>">&rarr;</span>
                                            <span><a href="javascript:showHierarchy('<?php echo$boundary_id; ?>','<?php echo $parent_css_id; ?>')"><?php echo $boundary_name; ?></a></span>
                                            <div id="<?php echo $parent_css_id; ?>" style="visibility:hidden;"></div>
                                        </td>
                                        <td class="col-4">
                                            <ul>
                                                <li class="none-separator">
                                                    <a href="#add" onClick="fillFields('<?php echo(rawurlencode($boundary_id)); ?>',
                                                        '<?php echo (rawurlencode($boundary_name)); ?>',
                                                        '<?php echo (rawurlencode($boundary_type_id)); ?>',
                                                        '<?php echo (rawurlencode($parent_id)); ?>')">
                                                        Edit
                                                    </a>
                                                </li>
                                                <li><a href="javascript:boundaryAction('d','DELETE','<?php echo(rawurlencode($boundary_id)); ?>')"
                                                    class="del">Delete</a></li>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    <?php print form::close(); ?>
                </div>

                <!-- tabs -->
                <div class="tabs">
                    <!-- tabset -->
                    <a name="add"></a>
                    <ul class="tabset">
                        <li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit'); ?></a></li>
                    </ul>

                    <!-- tab -->
                    <div class="tab">
                        <?php print form::open(NULL,array('id' => 'boundaryMain', 'name' => 'boundaryMain')); ?>
                            <input type="hidden" id="boundary_id" name="boundary_id" value="<?php echo $form['boundary_id']; ?>" />
                            <input type="hidden" name="action" id="action" value="a" />
                            <div class="tab_form_item">
                                <strong><?php echo Kohana::lang('ui_servicedelivery.boundary_name');?></strong><br />
                                <?php print form::input('boundary_name', $form['boundary_name'], ' class="text long2"'); ?>
                            </div>
                            <div class="tab_form_item">
                                <?php echo Kohana::lang('ui_servicedelivery.boundary_type');?><br />
                                <span class="my-sel-holder"><?php print form::dropdown('boundary_type_id', $boundary_types, $form['boundary_type_id']); ?></span>
                            </div>
                            <div class="tab_form_item">
                                <?php echo Kohana::lang('ui_servicedelivery.parent_boundary');?><br />
                                <span class="my-sel-holder"><?php print form::dropdown('parent_id', $parent_boundaries, $form['parent_id']); ?></span>
                            </div>
                            <div style="clear: both"></div>
                            <div class="tab_form_item">
                                &nbsp;<br />
                                <input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" value="SAVE" />
                            </div>
                        <?php print form::close(); ?>
                    </div>
                </div>
            </div>