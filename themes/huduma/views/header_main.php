<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $header_block; ?>
	<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
</head>

<body id="page">
    <!-- header wrapper -->
    <div id="header-wrapper">
    	<!-- header -->
    	<div id="header">
    	    <!--header-left -->
            <div id="header-left">
        		<!-- logo -->
        		<div id="logo">
        		</div>
        		<!-- / logo -->

    			<!-- category filters -->
    			<div class="cat-filters clearingfix">
    			    <!--
    				<strong><?php echo strtolower(Kohana::lang('ui_main.category_filter'));?> <span>[<a href="javascript:toggleLayer('category_switch_link', 'category_switch')" id="category_switch_link"><?php echo Kohana::lang('ui_main.hide'); ?></a>]</span></strong>
    				-->
    			</div>

    			<ul id="category_switch" class="category-filters">
    				<li><a class="active" id="cat_0" href="#"><span class="swatch" style="background-color:<?php echo "#".$default_map_all;?>"></span><span class="category-title"><?php echo Kohana::lang('ui_main.all_categories');?></span></a></li>
    				<?php
    					foreach ($categories as $category => $category_info)
    					{
    						$category_title = $category_info[0];
    						$category_color = $category_info[1];
    						$category_image = '';
    						$color_css = 'class="swatch" style="background-color:#'.$category_color.'"';
    						if($category_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$category_info[2])) {
    							$category_image = html::image(array(
    								'src'=>Kohana::config('upload.relative_directory').'/'.$category_info[2],
    								'style'=>'float:right; padding-left:5px; width:24px; height:24px;'
    								));
    							$color_css = '';
    						}
    						echo '<li><a href="#" id="cat_'. $category .'"><span class="category-title">'.$category_title.'</span><span '.$color_css.'>'.$category_image.'</span></a>';
    						// Get Children
    						echo '<div class="hide" id="child_'. $category .'">';
                            if( sizeof($category_info[3]) != 0)
                            {
                                echo '<ul>';
                                foreach ($category_info[3] as $child => $child_info)
                                {
                                    $child_title = $child_info[0];
                                    $child_color = $child_info[1];
                                    $child_image = '';
                                    $color_css = 'class="swatch" style="background-color:#'.$child_color.'"';
                                    if($child_info[2] != NULL && file_exists(Kohana::config('upload.relative_directory').'/'.$child_info[2]))
                                    {
                                        $child_image = html::image(array(
                                                'src'=>Kohana::config('upload.relative_directory').'/'.$child_info[2],
                                                'style'=>'float:right; padding-right:5px;'
                                                ));
                                        $color_css = '';
                                    }
                                    echo '<li style="padding-left:20px;"><a href="#" id="cat_'. $child .'"><span '.$color_css.'>'.$child_image.'</span><span class="category-title">'.$child_title.'</span></a></li>';
                                }
                                echo '</ul>';
                            }
						echo '</div></li>';
    					}
    				?>
    			</ul>
    			<!-- / category filters -->

        		<div class="clearingfix"></div>
    		</div>
    		<!-- /header-left -->

            <!-- right header -->
            <div id="header-right">
                <!-- mainmenu -->
        		<div id="mainmenu" class="clearingfix">
        			<ul>
        				<?php nav::main_tabs($this_page); ?>
        			</ul>
					
					<div id="topright-menubar"></div>

        		</div>
                <!-- /mainmenu -->

        		<!-- content column -->
        		<div id="content" class="clearingfix">
        			<div class="floatbox">
        				<!-- filters -->
        				<div class="filters clearingfix">
                            <?php
                            /*
        					<div style="float:left; width: 100%">
        						<strong><?php echo Kohana::lang('ui_main.filters'); ?></strong>
        						<ul>
        							<li><a id="media_0" class="active" href="#"><span><?php echo Kohana::lang('ui_main.reports'); ?></span></a></li>
        							<li><a id="media_4" href="#"><span><?php echo Kohana::lang('ui_main.news'); ?></span></a></li>
        							<li><a id="media_1" href="#"><span><?php echo Kohana::lang('ui_main.pictures'); ?></span></a></li>
        							<li><a id="media_2" href="#"><span><?php echo Kohana::lang('ui_main.video'); ?></span></a></li>
        							<li><a id="media_3" href="#"><span><?php echo Kohana::lang('ui_main.all'); ?></span></a></li>
        						</ul>
        					</div>
                            */
                            ?>
        					<?php
        					// Action::main_filters - Add items to the main_filters
        					Event::run('ushahidi_action.map_main_filters');
        					?>
        				</div>
        				<!-- / filters -->

        				<?php
        				// Map Blocks
        				echo $div_map;
        				?>

                        <!-- how to report -->
                        <?php
                        if (Kohana::config('settings.allow_reports')): ?>
                        <!-- additional content -->
                        <div class="additional-content">

                            <h5><?php echo Kohana::lang('ui_main.how_to_report'); ?></h5>
                            <div style="float: left; margin-left: 120px; margin-top: -26px;">
                            <ol>
        					<?php if (!empty($phone_array)): ?>
                                <li><?php echo Kohana::lang('ui_main.sms').": "; ?>
                                <?php
                                foreach ($phone_array as $phone)
                                {
                                    echo "<strong>". $phone ."</strong>";
                                    if ($phone != end($phone_array)) echo " or ";
                                }
                                ?>
                                </li>
                            <?php endif; ?>

                            <?php if (!empty($report_email)): ?>
        					<li><?php echo Kohana::lang('ui_main.email').": "; ?> <a href="mailto:<?php echo $report_email?>"><?php echo $report_email?></a></li>
                            <?php endif; ?>
        					
                            <?php if (!empty($twitter_hashtag_array)): ?>
                            <li>
                            <?php echo Kohana::lang('ui_main.twitter').": "; ?>
                            <?php
                                foreach ($twitter_hashtag_array as $twitter_hashtag)
                                {
                                    echo "<strong>". $twitter_hashtag ."</strong>";
                                    if ($twitter_hashtag != end($twitter_hashtag_array)) {
                                        echo " or ";
                                    }
                                }
                            ?>
                            </li>
                            <?php endif; ?>

                            <li><a href="<?php echo url::site() . 'reports/submit/'; ?>"><?php echo Kohana::lang('ui_main.report_option_4'); ?></a></li>
        				</ol>
                            </div>

                    </div>
                    <!-- /additional content -->
                    
                    <?php endif; ?>
                    <!-- /how to report -->

        			</div>
        		</div>
        		<!-- / content column -->

                <div id="clearingfix"></div>
            </div>
            <!-- /right header -->

    	</div>
    	<!-- / header -->
	</div>
	<!-- /header wrapper-->

	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">
