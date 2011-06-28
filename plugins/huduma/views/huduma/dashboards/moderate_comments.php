<?php
/**
 * Comment moderation view page
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
			<?php echo $dashboard_panel; ?>
        </div>
    </div>

    <div class="report-form">
        <div class="entity-name">
            <div class="row">
                <h1><?php echo Kohana::lang('ui_huduma.moderate_comments'); ?></h1>
            </div>
        </div>
    </div>
    
    <div class="dashboard_container">
        <?php print form::open(NULL, array('id'=>'entityForm', 'name'=>'entityForm')); ?>
            <input type="hidden" name="save" id="save" value="" />
            <input type="hidden" name="action" value="a" />

            <div class="panel_content">
                <?php if ($total_items > 0): ?>
                <ul class="dashboard_comments">
                <?php foreach ($comments as $comment): ?>
                    <?php
                    // IDs for the various links
                    $comment_spam_id = "comment_spam_".$comment->id;
                    $comment_notspam_id = "comment_notspam_".$comment->id;
                    $comment_delete_id = "comment_delete_".$comment->id;
                    $comment_undelete_id = "comment_undelete_".$comment->id;
                    ?>
                    <li class="dashboard_comment_box" style="clear: both;">
            			<div class="dashboard_comment_block">
                			<div>
                                <strong><?php echo $comment->comment_author ?></strong>&nbsp;
                                <span class="comment_date_time">
                                    <?php echo date('g:m a', strtotime($comment->comment_date)); ?>&nbsp;on&nbsp;
                                    <?php echo date('F j, Y', strtotime($comment->comment_date)); ?>
                                </span>
                			</div>
                			<div class="dashboard_comment_text">
                			    <p><?php echo $comment->comment_description; ?></p>
                			</div>
            			</div>
                        <span class="dashboard_comment_actions">
                        <?php if ($comment->comment_spam == 0): ?>
                            <a id="<?php echo $comment_spam_id; ?>" href="#<?php echo Kohana::lang('ui_main.spam'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_spam_id; ?>', '<?php echo $comment_notspam_id; ?>', 'spam')">
                                <?php echo Kohana::lang('ui_main.spam'); ?>
                            </a>
                            
                            <a id="<?php echo $comment_notspam_id; ?>" style="display:none;" href="#<?php echo Kohana::lang('ui_huduma.not_spam'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_notspam_id; ?>', '<?php echo $comment_spam_id; ?>', 'notspam')">
                                <?php echo Kohana::lang('ui_huduma.not_spam'); ?>
                            </a>
                            <?php else: ?>
                                <a id="<?php echo $comment_spam_id; ?>" style="display:none;" href="#<?php echo Kohana::lang('ui_main.spam'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_spam_id; ?>', '<?php echo $comment_notspam_id; ?>', 'spam')">
                                    <?php echo Kohana::lang('ui_main.spam'); ?>
                                </a>

                                <a id="<?php echo $comment_notspam_id; ?>" href="#<?php echo Kohana::lang('ui_huduma.not_spam'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_notspam_id; ?>', '<?php echo $comment_spam_id; ?>', 'notspam')">
                                    <?php echo Kohana::lang('ui_huduma.not_spam'); ?>
                                </a>
                        <?php endif; ?>
                        
                        <?php if ($comment->comment_active == 1): ?>
                            <a id="<?php echo $comment_delete_id; ?>" href="#<?php echo Kohana::lang('ui_main.delete'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_delete_id; ?>', '<?php echo $comment_undelete_id; ?>', 'delete')">
                                <?php echo Kohana::lang('ui_main.delete'); ?>
                            </a>
                            <a id="<?php echo $comment_undelete_id; ?>" style="display: none;" href="#<?php echo Kohana::lang('ui_huduma.undelete'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_undelete_id; ?>', '<?php echo $comment_delete_id; ?>', 'undelete')">
                                <?php echo Kohana::lang('ui_huduma.undelete'); ?>
                            </a>
                        <?php else: ?>
                            <a id="<?php echo $comment_delete_id; ?>" style="display:none;" href="#<?php echo Kohana::lang('ui_main.delete'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_delete_id; ?>', '<?php echo $comment_undelete_id; ?>', 'delete')">
                                <?php echo Kohana::lang('ui_main.delete'); ?>
                            </a>
                            <a id="<?php echo $comment_undelete_id; ?>" href="#<?php echo Kohana::lang('ui_huduma.undelete'); ?>" onclick="updateComment('<?php echo $comment->id; ?>', '<?php echo $comment_undelete_id; ?>', '<?php echo $comment_delete_id; ?>', 'undelete')">
                                <?php echo Kohana::lang('ui_huduma.undelete'); ?>
                            </a>
                        <?php endif; ?>
                        
                        </span>
                    </li>    
                <?php endforeach; ?>
                </ul>
                <?php endif; ?>
                
                <div class="row">
                    <?php echo $pagination; ?>
                </div>
            </div>
        
        <?php print form::close(); ?>
    </div>
    
</div>
