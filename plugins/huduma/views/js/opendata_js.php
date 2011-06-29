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
	
	var proj_4326 = new OpenLayers.Projection('EPSG:4326');
	var proj_900913 = new OpenLayers.Projection('EPSG:900913');
	var markerRadius = 4;
	var markerOpacity = 0.8;
	var selectControl;
	var map;
	
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
			})
		});
		
		// Event handler for the "Apply" button
		$("#apply_overlay").click(function(){
			// Get the selected category
			var category_id = $("#category_id").val();
			var facility_type = $("#facility_type").val();
			
			// Check if the heatmap layer already exists
			var layers = map.getLayersByName('Heatmap');
			for (var i = 0; i < layers.length; i++)
			{
				map.removeLayer(layers[i]);
			}
			layers = map.getLayersByName('Clusters');
			for (var i = 0; i < layers.length; i++)
			{
				map.removeLayer(layers[i]);
			}
			
			// Check the selected overlay option
			if ($("#heatmap_overlay").attr("checked"))
			{
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
				addClusterMarkers(category_id, facility_type);
			}
		});
		
	});
	
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
		setTimeout('map.addLayer(heatLayer)', 500);
	}
	
	/**
	 * Adds cluster markers overlay
	 */
	function addClusterMarkers(category_id, facility_type)
	{
		// Transform feature point coordinate to Spherical Mercator
		preFeatureInsert = function(feature)
		{
			var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
			OpenLayers.Projection.transform(point, proj_4326, proj_900913);
		};
		
		// Zoom level
		var zoomLevel = <?php echo ($default_zoom)? $default_zoom : 10; ?>
		
		// Marker style
		var m_style = getMarkerStyle();
		
		// The URL to use to fetch the clusters
		var fetchURL = '<?php echo url::site().'overlays/cluster/'?>' + '?z=' + zoomLevel + '&c=' + category_id + '&e=' + facility_type;
		
		// Create the overlay markers
        overlayMarkers = new OpenLayers.Layer.GML('Clusters', fetchURL,
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
		map.addLayer(overlayMarkers);

		// Add overlay markers to the list of feature selection items
		selectControl = new OpenLayers.Control.SelectFeature(overlayMarkers);
        map.addControl(selectControl);
        selectControl.activate();
        
		overlayMarkers.events.on({
			"featureselected": onFeatureSelect,
			"featureunselected": onFeatureUnselect
		});
	}
	
	function getMarkerStyle()
	{
		// Set Feature Styles
        style = new OpenLayers.Style({
            'externalGraphic': "${icon}",
            'graphicTitle': "${cluster_count}",
            pointRadius: "${radius}",
            fillColor: "${color}",
            fillOpacity: "${opacity}",
            strokeColor: "${color}",
            strokeWidth: "${strokeWidth}",
            strokeOpacity: "0.3",
            label:"${clusterCount}",
            //labelAlign: "${labelalign}", // IE doesn't like this for some reason
            fontWeight: "${fontweight}",
            fontColor: "#ffffff",
            fontSize: "${fontsize}"
        },
        {
            context:
            {
                count: function(feature)
                {
                    if (feature.attributes.count < 2)
                    {
                        return 2 * markerRadius;
                    }
                    else if (feature.attributes.count == 2)
                    {
                        return (Math.min(feature.attributes.count, 7) + 1) *
                        (markerRadius * 0.8);
                    }
                    else
                    {
                        return (Math.min(feature.attributes.count, 7) + 1) *
                        (markerRadius * 0.6);
                    }
                },
                fontsize: function(feature)
                {
                    feature_icon = feature.attributes.icon;
                    if (feature_icon!=="")
                    {
                        return "9px";
                    }
                    else
                    {
                        feature_count = feature.attributes.count;
                        if (feature_count > 1000)
                        {
                            return "20px";
                        }
                        else if (feature_count > 500)
                        {
                            return "18px";
                        }
                        else if (feature_count > 100)
                        {
                            return "14px";
                        }
                        else if (feature_count > 10)
                        {
                            return "12px";
                        }
                        else if (feature_count >= 2)
                        {
                            return "10px";
                        }
                        else
                        {
                            return "";
                        }
                    }
                },
                fontweight: function(feature)
                {
                    feature_icon = feature.attributes.icon;
                    if (feature_icon!=="")
                    {
                        return "normal";
                    }
                    else
                    {
                        return "bold";
                    }
                },
                radius: function(feature)
                {
                    feature_count = feature.attributes.count;
                    if (feature_count > 10000)
                    {
                        return markerRadius * 17;
                    }
                    else if (feature_count > 5000)
                    {
                        return markerRadius * 10;
                    }
                    else if (feature_count > 1000)
                    {
                        return markerRadius * 8;
                    }
                    else if (feature_count > 500)
                    {
                        return markerRadius * 7;
                    }
                    else if (feature_count > 100)
                    {
                        return markerRadius * 6;
                    }
                    else if (feature_count > 10)
                    {
                        return markerRadius * 5;
                    }
                    else if (feature_count >= 2)
                    {
                        return markerRadius * 3;
                    }
                    else
                    {
                        return markerRadius * 2;
                    }
                },
                strokeWidth: function(feature)
                {
                    feature_count = feature.attributes.count;
                    if (feature_count > 10000)
                    {
                        return 45;
                    }
                    else if (feature_count > 5000)
                    {
                        return 30;
                    }
                    else if (feature_count > 1000)
                    {
                        return 22;
                    }
                    else if (feature_count > 100)
                    {
                        return 15;
                    }
                    else if (feature_count > 10)
                    {
                        return 10;
                    }
                    else if (feature_count >= 2)
                    {
                        return 5;
                    }
                    else
                    {
                        return 1;
                    }
                },
                color: function(feature)
                {
                    return "#" + feature.attributes.color;
                },
                icon: function(feature)
                {
                    feature_icon = feature.attributes.icon;
                    if (feature_icon!=="")
                    {
                        return baseUrl + feature_icon;
                    }
                    else
                    {
                        return "";
                    }
                },
                clusterCount: function(feature)
                {
                    if (feature.attributes.count > 1)
                    {
                        if(jQuery.browser.msie && $.browser.version=="6.0")
                        { // IE6 Bug with Labels
                            return "";
                        }

                        feature_icon = feature.attributes.icon;
                        if (feature_icon!=="")
                        {
                            return "> " + feature.attributes.count;
                        }
                        else
                        {
                            return feature.attributes.count;
                        }
                    }
                    else
                    {
                        return "";
                    }
                },
                opacity: function(feature)
                {
                    feature_icon = feature.attributes.icon;
                    if (feature_icon!=="")
                    {
                        return "1";
                    }
                    else
                    {
                        return markerOpacity;
                    }
                },
                labelalign: function(feature)
                {
                    feature_icon = feature.attributes.icon;
                    if (feature_icon!=="")
                    {
                        return "c";
                    }
                    else
                    {
                        return "c";
                    }
                }
            }
        });
        return style;
	}
	
	/**
     * Display popup when feature selected
     */
    function onFeatureSelect(event)
    {
		selectedFeature = event.feature;
		zoom_point = event.feature.geometry.getBounds().getCenterLonLat();
		lon = zoom_point.lon;
		lat = zoom_point.lat;
		
		var thumb = "";
		if ( typeof(event.feature.attributes.thumb) != 'undefined' && 
			event.feature.attributes.thumb != '')
		{
			thumb = "<div class=\"infowindow_image\"><a href='"+event.feature.attributes.link+"'>";
			thumb += "<img src=\""+event.feature.attributes.thumb+"\" height=\"59\" width=\"89\" /></a></div>";
		}

		var content = "<div class=\"infowindow\">" + thumb;
		content += "<div class=\"infowindow_content\"><div class=\"infowindow_list\">"+event.feature.attributes.name+"</div>";
		content += "\n<div class=\"infowindow_meta\">";
		if ( typeof(event.feature.attributes.link) != 'undefined' &&
			event.feature.attributes.link != '')
		{
			content += "<a href='"+event.feature.attributes.link+"'><?php echo Kohana::lang('ui_main.more_information');?></a><br/>";
		}
		
		content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",1)'>";
		content += "<?php echo Kohana::lang('ui_main.zoom_in');?></a>";
		content += "&nbsp;&nbsp;|&nbsp;&nbsp;";
		content += "<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +",-1)'>";
		content += "<?php echo Kohana::lang('ui_main.zoom_out');?></a></div>";
		content += "</div><div style=\"clear:both;\"></div></div>";		

		if (content.search("<?php echo '<'; ?>script") != -1)
		{
			content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/<?php echo '<'; ?>/g, "&lt;");
		}
        
		// Destroy existing popups before opening a new one
		if (event.feature.popup != null)
		{
			map.removePopup(event.feature.popup);
		}
		
		popup = new OpenLayers.Popup.FramedCloud("chicken", 
			event.feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(100,100),
			content,
			null, true, onPopupClose);

		event.feature.popup = popup;
		map.addPopup(popup);
    }

    /**
     * Destroy Popup Layer
     */
	function onFeatureUnselect(event)
	{
		map.removePopup(event.feature.popup);
		event.feature.popup.destroy();
		event.feature.popup = null;
	}

    /**
     * Close Popup
     */
	function onPopupClose(evt)
	{
		// selectControl.unselect(selectedFeature);
		for (var i=0; i<map.popups.length; ++i)
		{
			map.removePopup(map.popups[i]);
		}
	}
