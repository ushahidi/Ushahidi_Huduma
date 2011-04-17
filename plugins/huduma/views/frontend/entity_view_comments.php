<?php
/**
 * View file for the static entity comments
 */
?>
<ul class="dashboard_comments">
    <h5><?php echo strtoupper(Kohana::lang('ui_main.comments')); ?></h5>
	<!-- comments -->
	<?php foreach ($comments as $comment): ?>
	    <li class="dashboard_comment_box" style="clear: both;" id="dashboard_comment_<?php echo $comment->id; ?>">
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
                <a href="#<?php echo Kohana::lang('ui_main.reply'); ?>" onclick="showCommentBox('dashboard_comment_<?php echo $comment->id; ?>', '<?php echo urlencode($comment->id);?>')">
                    <?php echo Kohana::lang('ui_main.reply'); ?>
                </a>
                <a href="#<?php echo Kohana::lang('ui_main.share'); ?>">
                    <?php echo Kohana::lang('ui_main.share'); ?>
                </a>
            </span>
			
			<div class="dashboard_comment_credibility">
                <div class="entity_rating_value" id="crating_<?php echo $comment->id; ?>">
                    <?php echo $comment->comment_rating; ?>
                </div>
                <span class="rating_tickers">
                    <span class="ratelink">
                        <a href="javascript:rating('<?php echo $comment->id; ?>','add','comment','cloader_<?php echo $comment->id; ?>')">
                        +</a>
                        <a href="javascript:rating('<?php echo $comment->id; ?>','subtract','comment','cloader_<?php echo $comment->id; ?>')">-</a>
                    </span>
                </span>
                <div>
                    <span id="cloader_<?php echo $comment->id; ?>" class="rating_loading" ></span>
                </div>
			</div>
			
			<div class="comment_box_holder"></div>
			<?php //TODO: Check if thare are any replies to this comment and generate another list ?>
			<?php //TODO: Adjust the table structure for dashboard comments to store replies to comments ?>
		</li>
	<?php endforeach; ?>
	<!-- /comments -->
</ul>
