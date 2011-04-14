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
						<?php if ($show_dashboard_panel): ?>
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
						<?php endif; ?>
                    </div>
                </div>

                <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
                    <input type="hidden" name="save" id="save" value="" />
                    <input type="hidden" name="action" value="a" />
                    
                    <div class="report-form">
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

							<?php if ($show_metadata): ?>
							<div class="row">
								<a href="javascript:showEntityMetadata()"><?php echo Kohana::lang('ui_huduma.view_additional_info'); ?></a>
							</div>
							<?php endif; ?>

							<!-- comments -->
							<?php foreach ($comments as $comment): ?>
							<div class="row">
								<h6><?php echo $comment->comment_author; ?></h6>
								<p><?php echo $comment->comment_description; ?></p>
							</div>
							<?php endforeach; ?>
							<!-- /comments -->

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

							<?php echo $entity_comments_form; ?>
                        </div>

                    </div>
                <?php print form::close(); ?>
            </div>
