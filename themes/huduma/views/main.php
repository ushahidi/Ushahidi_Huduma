<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox withright">

		<?php if($site_message != '') : ?>
		<div class="green-box">
			<h3><?php echo $site_message; ?></h3>
		</div>
		<?php endif; ?>

		<!-- left column -->
		<div id="pageColLeft">
			<div class="mainpage-left">
			    <div class="floatbox">
					<?php
						//>
						/** 
						 * Emmanuel Kala - 18/05/2011 
						 * NOTES: Removed display of KML overlays - not factored in design of home page
						 */		
						//>
					?>
			    </div>

				<?php 
					// Action: main_sidebar - Add items to the main sidebar
					Event::run('ushahidi_action.main_sidebar'); 
				?>
			</div>
		</div>
		<!-- / left column -->

		<!-- right column -->
		<div id="pageColRight">
			<div class="mainpage-middle">
		
		        <?php if (Kohana::config('settings.allow_reports')): ?>
		        <!-- additional content -->
		        <div class="additional-content">

		            <strong><?php echo Kohana::lang('ui_main.how_to_report'); ?>:</strong>
					<?php if (!empty($phone_array)): ?>
		                <span><?php echo Kohana::lang('ui_main.sms')." to"; ?>
		                <?php
		                foreach ($phone_array as $phone)
		                {
		                    echo "<strong>". $phone ."</strong>";
		                    if ($phone != end($phone_array)) echo " or ";
		                }
		                ?>
		                </span>
		            <?php endif; ?>

		            <?php if (!empty($report_email)): ?>
					| <span>
						<strong><?php echo Kohana::lang('ui_main.email').": "; ?></strong>
						<a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></span>
		            <?php endif; ?>
			
		            <?php if (!empty($twitter_hashtag_array)): ?>
					| <span>
						<strong><?php echo Kohana::lang('ui_main.twitter').": "; ?></strong>
			            <?php
		                foreach ($twitter_hashtag_array as $twitter_hashtag)
		                {
		                    echo "<strong>". $twitter_hashtag ."</strong>";
		                    if ($twitter_hashtag != end($twitter_hashtag_array)) {
		                        echo " or ";
		                    }
		                }
		            ?>
		            </span>
		            <?php endif; ?>
					
				</div>
				<?php endif; ?>
				
				<?php echo $div_timeline; ?>
			
			<?php
		        // Action::main_content - Add Items to the center column
		        Event::run('huduma_action.main_content');
				?>
			</div>
		</div>
		<!-- / right column -->

	</div>
</div>
<!-- / main body -->
