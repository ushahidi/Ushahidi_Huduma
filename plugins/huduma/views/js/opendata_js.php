<?php
/**
 * PHP-Javascript file for the opendata controller
 *
 * @author Ushahidi - http://ushahidi.com
 * @copyright Ushahidi - http://ushahidi.com
 */
?>
	<?php @require_once(PLUGINPATH.'huduma/views/js/map_common_js.php'); ?>

	/**
	 * Handles feature selection events for the shapefiles
	 *
	 */
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
				if (response.success)
				{
					$("#featureContent").fadeIn("slow");
					$("#featureContent").html(response.content);
					
					// Get the piechart data
					
					// Load the visualization API and the piechart package
					// google.load("visualization", "1", {packages :["corechart"]});
					
					// Set a callback when the Visualization API is loaded
					// google.setOnLoadCallback(function(){});
					
					// Draws the piechart
					drawPieChart = function(){
						// var data = new google.visualization.DataTable();
						// data.addColumn('string', 'Gender');
						// data.addColumn('number', 'Population');
						// data.addRows(2);
						// var index = 0;
						// $.each(response.piechartData, function(key, value){
						// 	data.setValue(index, 0, key);
						// 	data.setValue(index, 1, value);
						// 	index++;
						// });
						
						// $("#piechart_div").css("display", "block");
						// var chart = new google.visualization.PieChart($("#piechart_div"));
						// chart.draw(data, {width: 300, height: 240});
					}
				}
			}
		);
	}
	
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	var selectControl;
	var clusterMapLoaded = 0;
	var map;
	
	// Zoom level
	var zoomLevel = <?php echo ($default_zoom)? $default_zoom : 10; ?>
	
	// To hold the selected category
	var category_id;
	
	// To hold the selected facility type
	var facility_type;
	
	
	function mapZoomed()
	{
		if (clusterMapLoaded == 1)
		{
			removeLayers(['Clusters']);
			zoomLevel = map.getZoom();
			addClusterMarkers(category_id, facility_type);
		}
	}
	
	// To be executed when the page/document loads
	jQuery(function(){
		
		// The mapping stuff
		var colorMap = <?php echo $color_map; ?>
		
		// Initialize the map + options
		var options = {
			units: "m",
			numZoomLevels: 18,
			controls:[],
			projection: proj_900913,
			'displayProjection': proj_4326,
			eventListeners: {
				"zoomend" : mapZoomed
			}
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
				selectControl = new OpenLayers.Control.SelectFeature(countyLayer, selectOptions);
				
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
			});
		});
		
		// Event handler for the "Apply" button
		$("#apply_overlay").click(function(){
			// Get the selected category
			category_id = $("#category_id").val();
			facility_type = $("#facility_type").val();
			
			// Remove layers
			removeLayers(['Heatmap', 'Clusters']);
			
			// Check the selected overlay option
			if ($("#heatmap_overlay").attr("checked"))
			{
				clusterMapLoaded = 0;
				$.get('<?php echo url::site().'opendata/get_heatmap_data'?>', {category : category_id, facility_type: facility_type}, 
					function(data)
					{
						if (data.success && data.points.length > 0)
						{
							selectControl.deactivate();
							addHeatLayer(data.points);
						}
					}
				);
			}
			else if ($("#cluster_overlay").attr("checked"))
			{
				// Activate the select control
				selectControl.activate();
				
				// Add the cluster markers
				addClusterMarkers(category_id, facility_type);
			}
		});
		
	});
	
	function removeLayers(layers)
	{
		for (var i=0; i < layers.length; i++)
		{
			items = map.getLayersByName(layers[i]);
			for (var j=0; j < items.length; j++)
			{
				map.removeLayer(items[j]);
			}
		}
	}
	
	/**
	 * Creates a heatmap layer and adds it on the current map
	 */
	function addHeatLayer(points)
	{
		// Cerate the heatmap layer
		heatLayer = new Heatmap.Layer('Heatmap');
		
		$.each(points, function(i, item){
			var dataPoint = new OpenLayers.LonLat(item.longitude, item.latitude);
			dataPoint.transform(proj_4326, map.getProjectionObject());
			
			heatLayer.addSource(new Heatmap.Source(dataPoint));
		});
		
		// Once all the incidents have been fetched via cURL, render the heatmap
		heatLayer.defaultDensity = 0.08;
		heatLayer.setOpacity(0.65);
		
		// Add the heat layer to the map
		setTimeout(function() { map.addLayer(heatLayer); }, 500);
	}
	
	/**
	 * Adds cluster markers overlay
	 */
	function addClusterMarkers(cat_id, f_type)
	{
		// Transform feature point coordinate to Spherical Mercator
		preFeatureInsert = function(feature)
		{
			var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
			OpenLayers.Projection.transform(point, proj_4326, proj_900913);
		};
		
		// Marker style
		var m_style = getOverlaysMarkerStyle();
		
		// The URL to use to fetch the clusters
		var fetchURL = '<?php echo url::site().'overlays/cluster/'?>' + '?z=' + zoomLevel + '&c=' + cat_id + '&e=' + f_type;
		
		console.log("Fetch URL is: %s", fetchURL);
		
		// Create the overlay markers
        var overlayMarkers = new OpenLayers.Layer.GML('Clusters', fetchURL,
            {
                preFeatureInsert: preFeatureInsert,
                format: OpenLayers.Format.GeoJSON,
                projection: proj_4326,
                formatOptions: {
                    extractStyles: true,
                    extractAttributes: true
                },
                styleMap : new OpenLayers.StyleMap({
                    "default": m_style,
                    "select": m_style
                })
            }
        );
		// Add the markers layer to the map
		if (overlayMarkers != null)
		{
			map.addLayer(overlayMarkers);
			
			// Add overlay markers to the list of feature selection items
			selectControl = new OpenLayers.Control.SelectFeature(overlayMarkers);
			map.addControl(selectControl);
			selectControl.activate();
			
			overlayMarkers.events.on({
				"featureselected": onFeatureSelect,
				"featureunselected": onFeatureUnselect
			});

			// Set the loaded parameter
			clusterMapLoaded = 1;
		}
	}