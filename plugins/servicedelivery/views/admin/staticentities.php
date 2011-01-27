<?php
/**
 * Service provider view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Serviceprovider Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
 ?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('entitytypes'); ?>
                </h2>

            	<?php if ($form_error) : ?>
            		<!-- red-box -->
            		<div class="red-box">
            			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
            			<ul><?php echo Kohana::lang('ui_main.select_one');?></ul>
            		</div>
            	<?php endif; ?>

                <?php if ($form_saved): ?>
            		<!-- green-box -->
            		<div class="green-box" id="submitStatus">
            			<h3><?php echo Kohana::lang('ui_admin.service_providers');?> <?php echo $form_action; ?>
            			    <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a>
            			</h3>
            		</div>
            	<?php endif; ?>

            	<!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL, array('id' => 'entityTypeMain', 'name' => 'entityTypeMain')); ?>
                        <input type="hidden" name="action" id="action" value="" />
                        <input type="hidden" name="entity_type_id[]" id="service_provider_single" value="" />

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                            <input id="checkAllEntityTypes" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'entity_type_id[]' )" />
                                        </th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_servicedelivery.entity');?></th>
                                        <th class="col-3"><?php echo Kohana::lang('ui_admin.color');?></th>
                                        <th class="col-4"><?php echo Kohana::lang('ui_admin.actions');?></th>
                                    </tr>
                                </thead>

                                <!-- table footer -->
                                <tfoot>
                                    <tr class="foot">
                                        <td colspan="4">
                                            <?php echo $pagination; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                                <!-- /table footer -->

                            <tbody>
                            <?php if ($total_items == 0): ?>
                                <tr>
                                    <td colspan="4" class="col">
                                        <h3><?php echo Kohana::lang('ui_admin.no_result_display_msg');?></h3>
                                    </td>
                                </tr>
                            <?php endif; ?>

                            <?php
                                foreach ($entity_types as $type)
                                {
                                    $entity_type_id = $type->id;
                                    $type_name = $type->type_name;
                                    $entity_type_color = $type->entity_type_color;
                                    $entity_type_image = $type->entity_type_image;

                                ?>
                                    <tr>
                                        <td class="col-1">
                                            <input name="entity_type_id[]" id="entity_type" value="<?php echo $entity_type_id; ?>" type="checkbox" class="check-box"/>
                                        </td>

                                        <td class="col-2">
                                            <?php echo $type_name; ?>
                                        </td>

                                        <td class="col-3">
                                        <?php if ( ! empty($entity_type_image)): ?>
                                            <img src="<?php url::base().Kohana::config('upload.relative_directory')."/".$entity_type_image; ?>">
                                            &nbsp;[<a href="javascript:entityTypeAction('i','DELETE ICON','<?php echo rawurlencode($entity_type_id); ?>')">delete</a>]
                                        <?php else: ?>
                                            <img src="<?php echo url::base()?>/swatch/c?=<?php $entity_type_color?>&w=30&h=30" />
                                        <?php endif; ?>
                                        </td>

                                        <td class="col-4">

                                            <!-- actions -->
                                            <ul>
                                                <li class="none-separator">
                                                    <a href="#add">
                                                        <?php echo strtoupper(Kohana::lang('ui_admin.edit_action')); ?>
                                                    </a>
                                                </li>
                                                <li class="none-separator">
                                                    <a href="#" class="none-separator" onclick="entityTypeAction('d','DELETE', '<?php echo $entity_type_id; ?>');">
                                                        <?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?>
                                                    </a>
                                                </li>
                                            </ul>
                                            <!-- /actions -->

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
            </div>