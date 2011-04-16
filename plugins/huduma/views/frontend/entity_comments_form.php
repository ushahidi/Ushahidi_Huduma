<?php
/**
 * View page for the static entity comments form
 */
?>
<?php print form::open(NULL, array('id'=>'entityCommentForm', 'name'=>'entityCommentForm')); ?>

	<div class="dashboard_comment_form">
		<?php if ($form_error) : ?>
		<!-- red-box -->
        <div class="red-box">
			<h3><?php echo Kohana::lang('ui_main.error'); ?></h3>
			<ul>
             <?php foreach($errors as $error_item => $description): ?>
             	<?php print (!$description)? "" : "<li>".$description."</li>"; ?>
			<?php endforeach; ?>
			</ul>
		</div>
		<!-- /red-box -->
		<?php endif; ?>

		<?php if ($form_saved): ?>
		<!-- green-box -->
		<div class="green-box" id="submitStatus">
			<h3><?php echo Kohana::lang('ui_main.comment_saved'); ?></h3>
		</div>
		<!-- /green-box-->
		<?php endif; ?>

		<h4><?php echo Kohana::lang('ui_main.leave_a_comment'); ?></h4>
		
		<div class="row" style="float: left">
			<h4><?php echo Kohana::lang('ui_main.comments'); ?>:</h4>
			<?php print form::textarea('comment_description', $form['comment_description'], ' cols="38" rows="11" class="comment_field text"'); ?>
		</div>
		
		<div style="float: right">
    		<div class="row">
    			<h4><?php echo Kohana::lang('ui_main.name'); ?>:</h4>
    			<?php print form::input('comment_author', $form['comment_author'], ' class="comment_field text'); ?>
    		</div>

    		<div class="row">
    			<h4><?php echo Kohana::lang('ui_main.email'); ?>:</h4>
    			<?php print form::input('comment_email', $form['comment_email'], ' class="comment_field text"'); ?>
    		</div>

    		<div class="row">
    			<h4><?php echo Kohana::lang('ui_main.security_code'); ?></h4>
    			<?php print $captcha->render(); ?><br>
    			<?php print form::input('captcha', $form['captcha'], ' class="text comment_field"'); ?>
    		</div>
		</div>
		
		<div class="row" style="clear: both; padding: 10px 0;">
			<?php print form::input(array('type'=>'submit', 'name' =>'comment_submit', 'class'=>'huduma_button'), Kohana::lang('ui_huduma.submit_comment')); ?>
		</div>
	</div>
<?php print form::close(); ?>
