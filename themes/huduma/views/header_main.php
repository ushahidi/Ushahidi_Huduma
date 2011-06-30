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
    <div id="mainpage-header">
    	<!-- header -->
    	<div id="header">
    	    <!--header-left -->
            <div id="header-left">
				<div class="row" style="float: right; margin-bottom: 10px; ">
					<?php print form::input('livesearch_bar', '', ' class="mainpage_search text" placeholder="'.Kohana::lang('ui_huduma.search_huduma').'"'); ?>
					<div id="livesearch_results" style="display:none;"></div>
				</div>
				<div style="clear: both;"></div>
        		<!-- logo -->
        		<div id="logo">
        		</div>
        		<!-- / logo -->

    			<!-- category filters -->
    			<div class="cat-filters"></div>

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
    						echo '<li><a href="#" id="cat_'. $category .'">'
								. '<span class="category-title">'.$category_title.'</span>'
								. '<span '.$color_css.'>'.$category_image.'</span>'
								. '</a>'
								. '</li>';
    					}
    				?>
    			</ul>
    			<!-- / category filters -->

        		<div class="clearingfix"></div>
    		</div>
    		<!-- /header-left -->

            <!-- right header -->
            <div id="header-right">
				
				<div id="loginsection">
					<!-- login -->
					<div class="login">
						<?php if ($is_logged_in): ?>
							<?php print form::open(url::site().'dashboards/logout'); ?>
							<div class="row">
								<h3><?php echo $logged_in_user; ?></h3>
								<p class="dashboard_role_detail"><?php echo $static_entity_name; ?></p>
							</div>

							<div class="row login_panel_button">
								<?php print form::input(array('type'=>'submit', 'name'=> 'dashboard_logout', 'class'=>'submit'), Kohana::lang('ui_admin.logout')); ?>
							</div>
							<?php print form::close(); ?>
						<?php else: ?>
							<?php print form::open(url::site().'dashboards/login'); ?>
								<div class="row">
									<h5><?php echo Kohana::lang('ui_huduma.username_or_email'); ?></h5>
									<?php print form::input(array('name' => 'dashboard_username', 'class'=>'field', 'placeholder' => Kohana::lang('ui_huduma.username_placeholder')), ''); ?>
								</div>

								<div class="row">
									<h5><?php echo Kohana::lang('ui_main.password'); ?></h5>
									<?php print form::password(array('name' => 'dashboard_password', 'class'=>'field', 'placeholder' => Kohana::lang('ui_huduma.password_placeholder')), ''); ?>
								</div>
								<div>
									<?php print form::input(array('type'=>'submit', 'name'=>'login', 'class'=>'submit'), Kohana::lang('ui_main.reports_btn_submit'));?>
									<?php print form::input(array('type'=>'button', 'name'=>'register', 'class'=>'submit', 'onclick'=>'showRegistrationForm()'), Kohana::lang('ui_huduma.register'));?>
								</div>
							<?php print form::close(); ?>
						<?php endif; ?>
					</div>
					<!-- /login -->
				</div>
				<div style="clear:both;"></div>
                <!-- mainmenu -->
        		<div id="mainmenu" class="clearingfix">
        			<ul>
        				<?php nav::main_tabs($this_page); ?>
        			</ul>
        		</div>
                <!-- /mainmenu -->
            </div>
            <!-- /right header -->
			
			
    		<!-- content column -->
    		<div id="mainpageContent">
    			<div class="floatbox">
    				<!-- filters -->
    				<div class="filters clearingfix">
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
					<!-- /how to report -->

    			</div>
    		</div>
    		<!-- / content column -->

    	</div>
    	<!-- / header -->
	</div>
	<!-- /header wrapper-->

	<!-- wrapper -->
	<div class="rapidxwpr floatholder">
		<div id="facebox" style="display: none;">
			<div class="popup">
				<div class="content"></div>
				<a href="#" class="close">
					<?php print html::image(array('src' => 'plugins/huduma/views/images/closelabel.png', 'class'=> 'close_image')); ?>
				</a>
			</div>
		</div>

		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">
