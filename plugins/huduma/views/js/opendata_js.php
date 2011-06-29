<?php
/**
 * PHP-Javascript file for the opendata controller
 *
 * @author Ushahidi - http://ushahidi.com
 * @copyright Ushahidi - http://ushahidi.com
 */
?>
	// Handles feature selection events
	function shapeFileSelect(feature)
	{
		// Get the feature 
		featureId = feature.attributes.featureId;
		
		$("#featureContent").html("");
		$("#featureContent").hide();
		// Get the data items for that feature item
		$.get('<?php echo url::site()?>opendata/map_data/'+featureId,
			function(response)
			{
				// Show the infowindow
				if (response != null && response.length > 0)
				{
					$("#featureContent").fadeIn("slow");
					$("#featureContent").html(response);
				}
			}
		);
	}
	
	// To be executed when the page/document loads
	jQuery(function(){
		
		// The mapping stuff
		var colorMap = <?php echo $color_map; ?>
		// Initialize the map + options
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		var options = {
			units: "m",
			numZoomLevels: 18,
			controls:[],
			projection: proj_900913,
			'displayProjection': proj_4326
			};
		map = new OpenLayers.Map('opendataMap', options);
		<?php echo map::layers_js(FALSE); ?>
        map.addLayers(<?php echo map::layers_array(FALSE); ?>);
        map.addControl(new OpenLayers.Control.Navigation());
        map.addControl(new OpenLayers.Control.PanZoom());
        map.addControl(new OpenLayers.Control.MousePosition(
			{ div: 	document.getElementById('mapMousePosition'), numdigits: 5
		}));
            
        map.addControl(new OpenLayers.Control.Scale('mapScale'));
        map.addControl(new OpenLayers.Control.ScaleLine());
        map.addControl(new OpenLayers.Control.LayerSwitcher());

		
		//> Stlying for the GeoJSON layer
		var context = {
			getColor: function(feature) {
				var f = feature;
				f.attributes.color = "#"+colorMap[f.attributes.featureId];
				feature = f;
				
				return feature.attributes["color"];
				
			}
		};
		
		var template = {
			fillOpacity: 0.85,
			strokeColor: "#000000",
			strokeWidth: 1,
			fillColor: "${getColor}"
		};
		
		var s_map = new OpenLayers.StyleMap(
			{
				'default': new OpenLayers.Style(template, {context: context})
			});
		
		// Create the GeoJSON layer
		var countyLayer = new OpenLayers.Layer.GML("Kenya Counties", "<?php echo url::base(); ?>media/uploads/Kenya_Counties.json",
			{
				format: OpenLayers.Format.GeoJSON,
				projection: proj_4326,
				styleMap: s_map
			});
		
		setTimeout(function() { 
			if (countyLayer != null) {
				map.addLayer(countyLayer); 
				
				var selectOptions  = { hover: false, toggle: true, onSelect: shapeFileSelect };
				var selectControl = new OpenLayers.Control.SelectFeature(countyLayer, selectOptions);
				
				map.addControl(selectControl);
				selectControl.activate();
			}
		}, 1500);
		
		// Set the map centre
		// create a lat/lon object
		myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
		myPoint.transform(proj_4326, map.getProjectionObject());
		map.setCenter(myPoint, <?php echo ($default_zoom) ? $default_zoom : 10; ?>);
		
		jQuery(document).ready(function(){
			jQuery(".breakdown-stats").horizontalBarGraph({interval: <?php echo $interval; ?>});
		});
		
		// Event handlers for the filters
		$("#category_id").change(function(){
			category_id  = $(this).val();
			
			// Get the facilities for that category
			$.get('<?php echo url::site(); ?>opendata/get_facilities/'+category_id, function(response) {
				if (response != null && response != '') {
					$("#facility_type").html(response);
				}
			})
		});
		
	});