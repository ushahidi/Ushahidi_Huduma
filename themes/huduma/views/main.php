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
