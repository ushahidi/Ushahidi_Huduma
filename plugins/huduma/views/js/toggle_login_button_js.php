<script type="text/javascript" charset="utf-8">
	// jQuery.noConflict();
	jQuery(document).ready(function(){
		jQuery('#d_login_button').click(function(){
			var display = jQuery(".login").css("display");

			if (display == "none") {
				jQuery(".login").show('fast');
			} else {
				jQuery(".login").hide('fast');
			}
			
		});
	});
</script>