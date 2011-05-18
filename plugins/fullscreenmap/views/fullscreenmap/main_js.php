<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function() {
	var orig_width = jQuery("#map").width();
	var orig_height = jQuery("#map").height();
	
	currZoom = map.getZoom();
	currCenter = map.getCenter();
	
	jQuery(".fullscreenmap_click").colorbox({
		width:"100%", 
		height:"100%", 
		inline:true, 
		href:"#map",
		// Resize Map DIV and Refresh
		onComplete:function(){
		    jQuery("#map").width("99%");
			jQuery("#map").height("99%");
//			$("#map").append(<?php echo $categories_view;?>);
//			$(".fullscreenmap_cats").draggable( { handle: 'h2' } );
			map.setCenter(currCenter, currZoom, false, false);
		},
		// Return DIV to original state
		onClosed:function(){
			jQuery("#map").width(orig_width);
			jQuery("#map").height(orig_height);
			jQuery("#map").show();
			map.setCenter(currCenter, currZoom, false, false);
		}
	});
});
</script>