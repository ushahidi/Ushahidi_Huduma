<?php
/**
 * View page for the static entity comments
 */
?>
<?php print form::open(NULL, array('id'=>'entityCommentForm', 'name'=>'entityCommentForm')); ?>
	<div class="dashboard-comment">
		<h3><?php echo Kohana::lang('ui_main.leave_a_comment'); ?></h3>
		<div class="row">
			<h4><?php echo Kohana::lang('ui_main.name'); ?>:</h4>
			<?php print form::input(array('type'=>'text', 'name'=>'comment_author', 'id'=>'comment_author', 'class'=>'comment_field text'), ''); ?>
		</div>

		<div class="row">
			<h4><?php echo Kohana::lang('ui_main.email'); ?>:</h4>
			<?php print form::input(array('type'=>'text', 'name'=>'comment_email', 'id'=>'commebt_email', 'class'=>'comment_field text'), ''); ?>
		</div>

		<div class="row">
			<h4><?php echo Kohana::lang('ui_main.comments'); ?>:</h4>
			<?php print form::textarea(array('name'=>'comment_description', 'id'=>'comment_description', 'cols'=>'75', 'rows'=>'8', 'class'=>'comment_field text')); ?>
		</div>

		<div class="row">
			<?php print form::input(array('type'=>'submit', 'name' =>'comment_submit', 'class'=>'huduma_button'), Kohana::lang('ui_huduma.submit_comment')); ?>
		</div>
	</div>
<?php print form::close(); ?>
