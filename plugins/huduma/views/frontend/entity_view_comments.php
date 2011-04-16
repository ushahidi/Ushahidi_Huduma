<?php
/**
 * View file for the static entity comments
 */
?>
<div class="dashboard_comments">
    <h5><?php echo strtoupper(Kohana::lang('ui_main.comments')); ?></h5>
	<!-- comments -->
	<?php foreach ($comments as $comment): ?>
	    <div class="dashboard_comment_box" style="clear: both;">
			<div class="dashboard_comment_block">
			<div>
                <strong><?php echo $comment->comment_author ?></strong>&nbsp;(<?php echo date('j M y', strtotime($comment->comment_date)); ?>)
			</div>
			<div>
				<?php echo $comment->comment_description; ?>
			</div>
			</div>
			
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
		</div>
	<?php endforeach; ?>
	<!-- /comments -->
</div>
