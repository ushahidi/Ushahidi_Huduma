<?php
 /**
  * View page for the report comments form
  *
  * PHP version 5
  * LICENSE: This source file is subject to LGPL license
  * that is available through the world-wide-web at the following URI:
  * http://www.gnu.org/copyleft/lesser.html
  * @author     Ushahidi Team <team@ushahidi.com>
  * @package    Ushahidi - http://source.ushahididev.com
  * @module     Static Entity Controller
  * @copyright  Ushahidi - http://www.ushahidi.com
  * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
  */
?>
	<div class="dashboard_comment_form">
        <?php print form::open(NULL, array('id'=>'entityCommentForm', 'name'=>'entityCommentForm')); ?>
            <?php print form::hidden('incident_id', $incident_id);?>
            
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
    			<h3><?php echo Kohana::lang('ui_huduma.comment_saved'); ?></h3>
    		</div>
    		<!-- /green-box-->
    		<?php endif; ?>
			
			<div class="report_comments_area">
			    <?php if ($is_dashboard_user): ?>
        		<div class="row">
                    <h4><?php echo Kohana::lang('ui_huduma.comment_on_issue'); ?></h4>
        			<?php print form::textarea('comment_description', $form['comment_description'], ' class="text" style="padding:0; height: 100px; width: 100%;"'); ?>
        		</div>
			    <?php else: ?>
	    		<div class="row" style="float: left;">
	                <h4><?php echo Kohana::lang('ui_huduma.comment_on_issue'); ?></h4>
	    			<?php print form::textarea('comment_description', $form['comment_description'], ' cols="35" rows="11" class="comment_field text"'); ?>
	    		</div>
		
	    		<div style="float: right;">
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
	        			<?php print form::input('captcha', '', ' class="text comment_field"'); ?>
	        		</div>
	    		</div>
	    		<?php endif; ?>
			</div>
		
			
    		<div class="row" style="clear: both; padding: 10px 0;">
    		    <div class="row" style="margin-bottom: 15px; padding-bottom: 15px;">
        		    <ul class="comment_form_buttons">
            			<li>
            			    <?php print form::input(array('type'=>'submit', 'name' =>'comment_submit', 'class'=>'huduma_button'), 
            			        Kohana::lang('ui_main.comment')); ?>
            			</li>
            			<li>
							<?php 
								if ($is_dashboard_user)
								{
									print form::input(array('type'=>'submit', 'name'=>'comment_close', 'class'=>'huduma_button'),
										Kohana::lang('ui_huduma.comment_close'));
								}
								else
								{
									print form::input(array('type'=>'button', 'name' =>'comment_cancel',  'id'=>'comment_cancel', 'class'=>'huduma_button', 
										'style'=>'display: none;'), Kohana::lang('ui_main.cancel'));
								}		
							?>
            			</li>
        			</ul>
    			</div>
    		</div>
    		
        <?php print form::close(); ?>
	</div>