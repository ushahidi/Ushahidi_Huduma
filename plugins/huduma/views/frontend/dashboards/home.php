<?php
/**
 * Dashboard home view page.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Home Controller
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

							<?php //if ($has_metadata): ?>
							<div class="row">
							[<a href="javascript:showEntityMetadata()"><?php echo Kohana::lang('ui_huduma.view_additional_info'); ?></a>]
							</div>
							<?php //endif; ?>

							<div class="sms-holder">
								<!-- comments -->
								<?php foreach ($comments as $comment): ?>
								<div clas="row">
									<h6><?php echo $comment->commet_author; ?></h6>
									<p><?php echo $comment->comment_description; ?></p>
								</div>
								<?php endforeach; ?>
								<!-- /comments-->
							</div>

							<!-- comment form -->
							<?php echo $entity_comments_form ?>
							<!-- /comment -->

                        </div>

                    </div>
                <?php print form::close(); ?>
            </div>
