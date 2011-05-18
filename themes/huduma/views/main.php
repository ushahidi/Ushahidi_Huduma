<!-- main body -->
<div id="main" class="clearingfix">
	<div id="mainmiddle" class="floatbox withright">

	<?php if($site_message != '') { ?>
		<div class="green-box">
			<h3><?php echo $site_message; ?></h3>
		</div>
	<?php } ?>

		<!-- right column -->
		<div id="right" class="clearingfix">
	        <?php echo $div_timeline; ?>
			<?php
	        // Action::main_content - Add Items to the center column
	        Event::run('huduma_action.main_content');
			?>
		</div>
		<!-- / right column -->

		<!-- content column -->
		<div id="left" class="clearingfix">
	        <div class="floatbox">
        		<?php
        		if ($layers)
        		{
        			?>
        			<!-- Layers (KML/KMZ) -->
        			<div class="cat-filters clearingfix" style="margin-top:20px;">
        				<strong><?php echo Kohana::lang('ui_main.layers_filter');?> <span>[<a href="javascript:toggleLayer('kml_switch_link', 'kml_switch')" id="kml_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
        			</div>
        			<ul id="kml_switch" class="category-filters">
        				<?php
        				foreach ($layers as $layer => $layer_info)
        				{
        					$layer_name = $layer_info[0];
        					$layer_color = $layer_info[1];
        					$layer_url = $layer_info[2];
        					$layer_file = $layer_info[3];
        					$layer_link = (!$layer_url) ?
        						url::base().Kohana::config('upload.relative_directory').'/'.$layer_file :
        						$layer_url;
        					echo '<li><a href="#" id="layer_'. $layer .'"
        					onclick="switchLayer(\''.$layer.'\',\''.$layer_link.'\',\''.$layer_color.'\'); return false;"><div class="swatch" style="background-color:#'.$layer_color.'"></div>
        					<div>'.$layer_name.'</div></a></li>';
        				}
        				?>
        			</ul>
        			<!-- /Layers -->
        			<?php
        		}
        		?>


        		<?php
        		if ($shares)
        		{
        			?>
        			<!-- Layers (Other Ushahidi Layers) -->
        			<div class="cat-filters clearingfix" style="margin-top:20px;">
        				<strong>
							<?php echo Kohana::lang('ui_main.other_ushahidi_instances');?> 
							<span>[<a href="javascript:toggleLayer('sharing_switch_link', 'sharing_switch')" id="sharing_switch_link">
								<?php echo Kohana::lang('ui_main.hide'); ?></a>]
							</span>
						</strong>
        			</div>
        			<ul id="sharing_switch" class="category-filters">
        				<?php
        				foreach ($shares as $share => $share_info)
        				{
        					$sharing_name = $share_info[0];
        					$sharing_color = $share_info[1];
        					echo '<li><a href="#" id="share_'. $share .'"><div class="swatch" style="background-color:#'.$sharing_color.'"></div>
        					<div>'.$sharing_name.'</div></a></li>';
        				}
        				?>
        			</ul>
        			<!-- /Layers -->
        			<?php
        		}
        		?>

        		<br />
		    </div>
			<?php 
				// Action: main_sidebar - Add items to the main sidebar
				Event::run('ushahidi_action.main_sidebar'); 
			?>
		</div>
		<!-- / content column -->

	</div>
</div>
<!-- / main body -->
