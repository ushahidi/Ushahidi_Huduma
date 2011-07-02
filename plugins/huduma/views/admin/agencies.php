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
                    <?php navigator::subtabs('agencies'); ?>
                </h2>

                <!-- tabs -->
                <div class="tabs">
                    <!-- tabset -->
					<ul class="tabset">
						<li><a href="<?php echo url::site() ?>admin/agencies" class="active"><?php echo Kohana::lang('ui_huduma.view_agencies'); ?></a></li>
						<li><a href="<?php echo url::site() ?>admin/agencies/edit"><?php echo Kohana::lang('ui_huduma.add_edit_agency');?></a></li>
						<li><a href="<?php echo url::site() ?>admin/agencies/types"><?php echo Kohana::lang('ui_huduma.agency_types');?></a></li>
					</ul>
                    <!-- /tabset -->

            		<div class="tab">
            			<ul>
            				<li><a href="#" onclick="agencyAction('d','DELETE', '');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_action')) ;?></a></li>
            				<li><a href="#" onclick="agencyAction('x','DELETE ALL ', '000');"><?php echo strtoupper(Kohana::lang('ui_admin.delete_all')) ;?></a></li>
            			</ul>
            		</div>
                </div>
                <!-- /tabs -->

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
            			<h3><?php echo Kohana::lang('ui_huduma.agencies');?> <?php echo $form_action; ?>
            			    <a href="#" id="hideMessage" class="hide"><?php echo Kohana::lang('ui_main.hide_this_message');?></a>
            			</h3>
            		</div>
            	<?php endif; ?>
                
            	<!-- report-table -->
                <div class="report-form">
                    <?php print form::open(NULL, array('id' => 'agencyMain', 'name' => 'agencyMain')); ?>
                        <input type="hidden" name="action" id="action" value="" />
                        <input type="hidden" name="agency_id[]" id="agency_single" value="" />

                        <div class="table-holder">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="col-1">
                                        <input id="checkAllAgencies" type="checkbox" class="check-box" onclick="CheckAll( this.id, 'agency_id[]' )" /></th>
                                        <th class="col-2"><?php echo Kohana::lang('ui_huduma.agency');?></th>
                                        <th class="col-4"><?php echo Kohana::lang('ui_admin.actions');?></th>
                                    </tr>
                                </thead>

                                <!-- table footer -->
                                <tfoot>
                                    <tr class="foot">
                                        <td colspan="3">
                                            <?php echo $pagination; ?>
                                        </td>
                                    </tr>
                                </tfoot>
                                <!-- /table footer -->

                            <tbody>
                                <?php if ($total_items == 0): ?>
                                    <tr>
                                        <td colspan="3" class="col">
                                            <h3><?php echo Kohana::lang('ui_admin.no_result_display_msg');?></h3>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php
                                    foreach ($agencies as $agency)
                                    {
                                        $agency_id = $agency->id;
                                        $agency_name = $agency->agency_name;
                                        $description = $agency->description;
                                    ?>
                                        <tr>
                                            <td class="col-1">
                                                <input name="agency_id[]" id="agency" value="<?php echo $agency_id; ?>" type="checkbox" class="check-box"/>
                                            </td>

                                            <td class="col-2">
                                                <?php echo $agency_name; ?>
                                                <div><?php echo $description; ?></div>
                                            </td>

                                            <td class="col-4">

                                                <!-- actions -->
                                                <ul>
                                                    <li class="none-separator">
                                                        <a href="<?php echo url::site().'admin/agencies/edit/'.$agency_id ?>">
                                                            <?php echo Kohana::lang('ui_admin.edit_action'); ?>
                                                        </a>
                                                    </li>
                                                    <li class="none-separator">
                                                        <a href="#" class="del" onclick="agencyAction('d','DELETE', '<?php echo $agency_id; ?>');">
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
            </div>