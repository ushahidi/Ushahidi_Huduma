<?php
/**
 * Single Entity view page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Entities Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
            <div class="bg">
                <div id="content-section-left">
                    <div id="sidebar-left-content">
                        <div id="dash-board">
                            <div class="top"></div>
                            <div class="cntr">
                                <ul>
                                    <li><a href="#">Dashboard Home</a></li>
                                    <li><a href="#">Report Analysis</a></li>
                                    <li><a href="#">Report Locations</a></li>
                                </ul>
                                <p style="padding-bottom: 20px;"></p>
                            </div>
                            <div class="btm"></div>
                        </div>
                    </div>
                </div>

                <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
                    <!-- service provider form -->
                    <div class="report-form">
                        <?php if ($form_error) : ?>
                            <!-- red-box -->
                            <div class="red-box">
                                <h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
                                <ul>
                                 <?php
                                 foreach($errors as $error_item => $description)
                                 {
                                     print (!$description)? "" : "<li>".$description."</li>";
                                 }
                                 ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($form_saved): ?>
                            <!-- green-box -->
                            <div class="green-box" id="submitStatus">
                                <h3><?php echo Kohana::lang('ui_huduma.entity_saved'); ?></h3>
                            </div>
                        <?php endif; ?>

                        <!-- column -->
                        <div class="entity-name">
                            <div class="row">
                                <h1><?php echo $entity_name; ?></h1>
                            </div>
                        </div>

                        <!-- /column -->
                        <div style="float: left; margin-left: 40px; padding-top:20px;">
                            <div id="report-map" class="entity-map">
                                <div class="map-holder" id="map"></div>
                                <div style="clear:both"></div>
                            </div>

							<?php if ($has_metadata): ?>
							<div class="row">
							[<a href="javascript:showEntityMetadata()"><?php echo Kohana::lang('ui_huduma.view_additional_info'); ?></a>]
							</div>
							<?php endif; ?>
							
                        </div>


<!--                        <div class="comment-col">
                            <div class="comment-items">
                                <div class="comment-item">
                                    <p><strong>Anonymous Commented</strong></p>
                                </div>
                            </div>
                        </div>-->
						
						<!-- metadata -->
						<div id="metadata-dialog" title="<?php echo $entity_name.' - '.Kohana::lang('ui_huduma.additional_info'); ?>">
							<table class="metadata" id="metadata-list">
								<thead>
									<tr>
										<th>Item</th>
										<th><?php echo Kohana::lang('ui_huduma.value'); ?></th>
										<th><?php echo Kohana::lang('ui_huduma.as_of_year'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($metadata as $item): ?>
									<tr>
										<td><?php echo $item->label; ?></td>
										<td><?php echo $item->value; ?></td>
										<td><?php echo $item->as_of_year; ?></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<!-- /metadata -->

                    </div>
                <?php print form::close(); ?>
            </div>
