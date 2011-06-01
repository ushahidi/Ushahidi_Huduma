<div id="content">
	<div class="content-bg">
		<div class="page-title"><h1><?php echo $page_title ?></h1></div>
		<div style="clear: both;"></div>
		<div class="big-block">
			<div id="pageColLeft"></div>
			<div id="pageColRight">
				<div class="page_text">
					<?php  echo htmlspecialchars_decode($page_description); ?>
					<?php Event::run('ushahidi_action.page_extra', $page_id); ?>
				</div>
			</div>
			<div style="clear: both"></div>
		</div>
	</div>
</div>
