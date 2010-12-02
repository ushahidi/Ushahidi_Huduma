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
        		    <!--
        			<h1><?php //echo $site_name; ?></h1>
        			<span><?php //echo $site_tagline; ?></span>
        			-->
        		</div>
        		<!-- / logo -->
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
                <div id="clearingfix"></div>
            </div>
            <!-- /right header -->

    	</div>
    	<!-- / header -->
	</div>
	<!-- /header wrapper-->

	<!-- wrapper -->
	<div class="rapidxwpr floatholder">

		<div id="content-mainbar"></div>
		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">
