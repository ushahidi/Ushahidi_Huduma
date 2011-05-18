<script type="text/javascript" encoding="utf-8">
	jQuery(document).ready(function(){
		var r = Raphael("pieChartSummary");
		r.g.piechart(250, 250, 100, [<?php echo $chart_data; ?>], {colors:[<?php echo $chart_colors; ?>]});
	});
</script>
