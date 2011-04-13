<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		$('#d_login_button').click(function(){
			var display = $(".login").css("display");

			if (display == "none") {
				$(".login").show('fast');
			} else {
				$(".login").hide('fast');
			}
			
		});
	});
</script>