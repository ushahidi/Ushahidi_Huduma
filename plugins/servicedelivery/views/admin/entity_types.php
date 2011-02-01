<?php
/**
 * Static entity types view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Staticentity Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
 ?>
            <div class="bg">
                <h2>
                    <?php navigator::subtabs('entities'); ?>
                </h2>

                <!-- tabs -->
                <div class="tabs">
                    <!-- tabset -->
                    <ul class="tabset">
                        <li><a href="<?php echo url::site() ?>admin/entities"><?php echo Kohana::lang('ui_main.show_all'); ?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/entities/types" class="active"><?php echo Kohana::lang('ui_servicedelivery.entity_types');?></a></li>
                        <li><a href="<?php echo url::site() ?>admin/entities/edit"><?php echo Kohana::lang('ui_servicedelivery.add_edit_entity');?></a></li>
                    </ul>
                    <!-- /tabset -->
                    
            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="entityTypeAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="entityTypeAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
            			</ul>
            		</div>
                </div>
                <!-- /tabs -->

            	<?php if ($form_error) : ?>
            		<!-- red-box -->
            		<div class="red-box">
            			<h3><?php echo Kohana::lang('ui_main.error');?></h3>
            			<ul>
                        <?php if ( ! empty($errors)) :?>
                            <?php foreach($errors as $item => $description): ?>
                            <?php print(!$description)? '':'<li>'.$description.'</li>'; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <?php echo Kohana::lang('ui_main.select_one');?>
                        <?php endif; ?>
                        </ul>
            		</div>
            	<?php endif; ?>

                <?php if ($form_saved): ?>
            		<!-- green-box -->
            		<div class="green-box" id="submitStatus">
            			<h3><?php echo Kohana::lang('ui_servicedelivery.entity_type');?> <?php echo $form_action; ?>
            			    <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a>
            			</h3>
            		</div>
            	<?php endif; ?>

            	<!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL, array('id' => 'entityTypeListing', 'name' => 'entityTypeListing')); ?>
                        <input type="hidden" name="action" id="action" value="" />
                        <input type="hidden" name="entity_type_id[]" id="entity_type_single" value="" />

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                            <input id="checkAllEntityTypes" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'entity_type_id[]' )" />
                                        </th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_servicedelivery.entity_type_name');?></th>
                                        <th class="col-3"><?php echo Kohana::lang('ui_main.color');?></th>
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
                                    $category_id = $type->category_id;
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
                                            <img src="<?php echo url::base()?>swatch/?c=<?php echo $entity_type_color; ?>&w=30&h=30" >
                                        <?php endif; ?>
                                        </td>

                                        <td class="col-4">

                                            <!-- actions -->
                                            <ul>
                                                <li class="none-separator">
                                                    <a href="#add" onclick="fillFields('<?php echo rawurlencode($entity_type_id)?>', '<?php echo rawurlencode($type_name)?>', '<?php echo rawurlencode($category_id) ?>', '<?php echo rawurlencode($entity_type_color)?>');">
                                                        <?php echo Kohana::lang('ui_admin.edit_action'); ?>
                                                    </a>
                                                </li>
                                                <li class="none-separator">
                                                    <a href="#" class="del" onclick="entityTypeAction('d', 'DELETE', '<?php echo $entity_type_id; ?>');">
                                                        <?php echo Kohana::lang('ui_admin.delete_action') ;?>
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

				<div class="tabs">
					<!-- tabset -->
					<a name="add"></a>
					<ul class="tabset">
						<li><a href="#" class="active"><?php echo Kohana::lang('ui_main.add_edit');?></a></li>
					</ul>
					<!-- tab -->
					<div class="tab">
						<?php print form::open(NULL, array('enctype' =>'multipart/form-data', 'id' => 'entityTypeMain', 'name' => 'entityTypeMain')); ?>
						<input type="hidden" id="entity_type_id" name="entity_type_id" value="<?php echo $form['entity_type_id']; ?>" />
						<input type="hidden" name="action" id="action" value="a"/>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_servicedelivery.entity_type_name');?>:</strong><br />
							<?php print form::input('type_name', $form['type_name'], ' class="text long"'); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.category');?>:</strong><br />
							<?php print form::dropdown('category_id', $categories, $form['category_id']); ?>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.color');?>:</strong><br />
							<?php print form::input('entity_type_color', $form['entity_type_color'], ' class="text"'); ?>
                            <script type="text/javascript" charset="utf-8">
                                $(document).ready(function() {
                                    $('#entity_type_color').ColorPicker({
                                        onSubmit: function(hsb, hex, rgb) {
                                            $('#entity_type_color').val(hex);
                                        },
                                        onChange: function(hsb, hex, rgb) {
                                            $('#entity_type_color').val(hex);
                                        },
                                        onBeforeShow: function(hsb, hex, rgb) {
                                            $(this).ColorPickerSetColor(this.value);
                                        }
                                    })
                                    .bind('keyup', function() {
                                        $(this).ColorPickerSetColor(this.value);
                                    });
                                });
                            </script>
						</div>
						<div class="tab_form_item2">
							<strong><?php echo Kohana::lang('ui_main.image_icon');?>:</strong><br />
							<?php print form::upload('entity_type_image', '', ''); ?>
						</div>
                        <!-- metadata -->
                        <!-- /metadata -->
						<div class="tab_form_item">
							&nbsp;<br />
							<input type="image" src="<?php echo url::base() ?>media/img/admin/btn-save.gif" class="save-rep-btn" />
						</div>
						<?php print form::close(); ?>
					</div>
				</div>
                
            </div>