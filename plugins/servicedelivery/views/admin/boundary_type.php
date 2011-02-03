<?php
/**
 * Boundary type view page.
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
                        <li><a href="<?php echo url::site() ?>admin/servicedelivery"><?php echo Kohana::lang('ui_main.show_all'); ?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/servicedelivery/types" class="active"><?php echo Kohana::lang('ui_servicedelivery.boundary_types');?></a></li>
                    </ul>
                    <!-- /tabset -->

            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="boundaryTypeAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="boundaryTypeAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
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

                <?php if ($form_saved): ?> <!-- green-box -->
                <div class="green-box">
                    <h3><?php echo Kohana::lang('ui_servicedelivery.boundary_type');?> <?php echo $form_action; ?></h3>
                </div>
                <?php endif; ?>
                
                <!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL, array('id' =>'boundaryTypeListing','name' => 'boundaryTypeListing')); ?>
                        <input type="hidden" name="action" id="action" value="">
                        <input type="hidden" name="boundary_type_id[]" id="boundary_type_id_single" value="">

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                            <input id="checkAllBoundaryTypes" type="checkbox" class="check-box"  onclick="CheckAll(this.id, 'boundary_type_id[]')" />
                                        </th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_servicedelivery.boundary_type_name');?></th>
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
                                        <td colspan="3" class="col">
                                        <h3><?php echo Kohana::lang('ui_main.no_results');?></h3>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php
                                foreach ($boundary_types as $boundary_type)
                                {
                                    $boundary_type_id = $boundary_type->id;
                                    $boundary_type_name = $boundary_type->boundary_type_name;
                                    $parent_id = $boundary_type->parent_id;
                                ?>
                                    <tr>
                                        <td class="col-1">
                                            <input name="boundary_type_id[]" id="boundary_type" value="<?php echo $boundary_type_id; ?>" type="checkbox" class="check-box"/>
                                        </td>
                                        <td class="col-2">
                                            <div class="post"><h4><?php echo $boundary_type_name; ?></h4></div>
                                        </td>
                                        <td class="col-4">
                                            <ul>
                                                <li class="none-separator">
                                                    <a href="#add" onClick="fillFields('<?php echo(rawurlencode($boundary_type_id)); ?>','<?php echo(rawurlencode($boundary_type_name)); ?>', '<?php echo rawurlencode($parent_id)?>')">Edit</a>
                                                </li>
                                                <li class="none-separator">
                                                    <a href="javascript:boundaryTypeAction('d','DELETE','<?php echo(rawurlencode($boundary_type_id)); ?>')" class="del">Delete</a>
                                                </li>
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
                        <li><a href="#" class="active">ADD/EDIT</a></li>
                    </ul>

                    <!-- tab -->
                    <div class="tab">
                        <?php print form::open(NULL,array('enctype' => 'multipart/form-data', 'id' => 'boundarytypeMain', 'name' => 'boundarytypeMain')); ?>
                            <input type="hidden" id="boundary_type_id" name="boundary_type_id" value=<?php echo $form['boundary_type_id'] ?>"" />
                            <input type="hidden" name="action" id="action" value="a" />

                            <div class="tab_form_item">
                                <strong><?php echo Kohana::lang('ui_servicedelivery.boundary_type_name');?></strong><br />
                                <?php print form::input('boundary_type_name', $form['boundary_type_name'], ' class="text"'); ?>
                            </div>
                            <div class="tab_form_item">
                                <strong><?php echo Kohana::lang('ui_servicedelivery.parent_boundary_type');?>:</strong><br />
                                <?php print form::dropdown('parent_id', $parents_array, $form['parent_id']); ?>
                            </div>
                            <div style="clear: both"></div>
                            <div class="tab_form_item">
                                <input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" value="SAVE" />
                            </div>
                        <?php print form::close(); ?>
                    </div>
                </div>
            </div>
