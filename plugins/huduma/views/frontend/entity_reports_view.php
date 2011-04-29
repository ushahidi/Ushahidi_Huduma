<?php
/**
 * View file for the static entity comments
 */
?>
<div id="entity_view_column">
<ul class="dashboard_comments">
    <h5><?php echo strtoupper(Kohana::lang('ui_main.reports')); ?></h5>
	<!-- comments -->
	<?php if ($reports): ?>
	<?php foreach ($reports as $incident): ?>
	    <li class="dashboard_comment_box" id="dashboard_comment_<?php echo $comment->id; ?>">
			<div class="dashboard_comment_block">
    			<div>
                    <strong><?php echo $incident->incident_title ?></strong>&nbsp;
                    <span class="comment_date_time">
                        <?php echo date('g:m a', strtotime($incident->incident_date)); ?>&nbsp;on&nbsp;
                        <?php echo date('F j, Y', strtotime($incident->incident_date)); ?>
                    </span>
    			</div>
    			<div class="dashboard_comment_text">
    			    <p><?php echo $incident->incident_description; ?></p>
    			</div>
			</div>
            <span class="dashboard_comment_actions">
                <a href="#<?php echo Kohana::lang('ui_main.comment'); ?>" 
                    onclick="showCommentBox('dashboard_comment_<?php echo $incident->id; ?>', '<?php echo urlencode($comment->id);?>')">
                    <?php echo Kohana::lang('ui_main.comment'); ?>
                </a>
                <a href="#<?php echo Kohana::lang('ui_main.share'); ?>">
                    <?php echo Kohana::lang('ui_main.share'); ?>
                </a>
            </span>
			
			<div class="dashboard_comment_credibility">
                <div class="entity_rating_value" id="cloader_<?php echo $incident->id; ?>">
                    <?php echo $comment->comment_rating; ?>
                </div>
                <span class="rating_tickers">
                    <span class="ratelink">
                        <a href="javascript:rating('<?php echo $incient->id; ?>','add', 'cloader_<?php echo $incident->id; ?>')">
                        +</a>
                        <a href="javascript:rating('<?php echo $incident->id; ?>','subtract','cloader_<?php echo $incident->id; ?>')">-</a>
                    </span>
                </span>
			</div>
			
			<?php echo navigator::inline_comments($incident->id); ?>
			<div class="comment_box_holder"></div>	
		</li>
	<?php endforeach; ?>
	<?php endif; ?>
	<!-- /comments -->
</ul>
</div>