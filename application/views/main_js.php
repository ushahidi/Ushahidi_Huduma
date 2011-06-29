<?php
/**
 * Main cluster js file.
 * 
 * Server Side Map Clustering
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     API Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		// Map JS
		
		// Map Object
		var map;
		// Selected Category
		var currentCat;
		// Selected Layer
		var thisLayer;
		// WGS84 Datum
		var proj_4326 = new OpenLayers.Projection('EPSG:4326');
		// Spherical Mercator
		var proj_900913 = new OpenLayers.Projection('EPSG:900913');
		// Change to 1 after map loads
		var mapLoad = 0;
		// /json or /json/cluster depending on if clustering is on
		var default_json_url = "<?php echo $json_url ?>";
		// Current json_url, if map is switched dynamically between json and json_cluster
		var json_url = default_json_url;
		
		var baseUrl = "<?php echo url::base(); ?>";
		var longitude = <?php echo $longitude; ?>;
		var latitude = <?php echo $latitude; ?>;
		var defaultZoom = <?php echo $default_zoom; ?>;
		var markerRadius = <?php echo $marker_radius; ?>;
		var markerOpacity = "<?php echo $marker_opacity; ?>";
		
		var activeZoom = null;

		var gMarkerOptions = {baseUrl: baseUrl, longitude: longitude,
		                     latitude: latitude, defaultZoom: defaultZoom,
							 markerRadius: markerRadius,
							 markerOpacity: markerOpacity,
							 protocolFormat: OpenLayers.Format.GeoJSON};

         var selectFeatureItems = [];
         var selectControl = null;

        /**
         * Create the Markers Layer
         */
        function addMarkers(catID,startDate,endDate, currZoom, currCenter,
            mediaType, thisLayerID, thisLayerType, thisLayerUrl, thisLayerColor)
        {
            activeZoom = currZoom;

            if(activeZoom == '') {
                // Run the map overlay event
                <?php Event::run('ushahidi_action.main_map_overlay'); ?>
                return jQuery.timeline({categoryId: catID,
                           startTime: new Date(startDate * 1000),
                           endTime: new Date(endDate * 1000),
                           mediaType: mediaType
                          }).addMarkers(
                            startDate, endDate, gMap.getZoom(),
                            gMap.getCenter(), thisLayerID, thisLayerType,
                            thisLayerUrl, thisLayerColor, json_url);
            }
			
            setTimeout(function() {
                if(currZoom == activeZoom){
                    // Run the map overlay event
                    <?php Event::run('ushahidi_action.main_map_overlay'); ?>
                    return jQuery.timeline({categoryId: catID,
                           startTime: new Date(startDate * 1000),
                           endTime: new Date(endDate * 1000),
                           mediaType: mediaType
                          }).addMarkers(
                            startDate, endDate, gMap.getZoom(),
                            gMap.getCenter(), thisLayerID, thisLayerType,
                            thisLayerUrl, thisLayerColor, json_url);
                }else{
                    return true;
                }
            }, 2000);
        }

        /**
         * Display loader as Map Loads
         */
        function onMapStartLoad(event)
        {
            if ($("#loader"))
            {
                $("#loader").show();
            }

            if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
            {
                $("#OpenLayers\\.Control\\.LoadingPanel_4").show();
            }
        }

        /**
         * Hide Loader
         */
        function onMapEndLoad(event)
        {
            if ($("#loader"))
            {
                $("#loader").hide();
            }

            if ($("#OpenLayers\\.Control\\.LoadingPanel_4"))
            {
                $("#OpenLayers\\.Control\\.LoadingPanel_4").hide();
            }
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

        // Refactor Clusters On Zoom
        // *** Causes the map to load json twice on the first go
        // *** Need to fix this!
        function mapZoom(event)
        {
            // Prevent this event from running on the first load
            if (mapLoad > 0)
            {
                // Get Current Category
                currCat = $("#currentCat").val();

                // Get Current Start Date
                currStartDate = $("#startDate").val();

                // Get Current End Date
                currEndDate = $("#endDate").val();

                // Get Current Zoom
                currZoom = map.getZoom();

                // Get Current Center
                currCenter = map.getCenter();

                // Refresh Map
                addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
            }
        }

        function mapMove(event)
        {
            // Prevent this event from running on the first load
            if (mapLoad > 0)
            {
                // Get Current Category
                currCat = $("#currentCat").val();

                // Get Current Start Date
                currStartDate = $("#startDate").val();

                // Get Current End Date
                currEndDate = $("#endDate").val();

                // Get Current Zoom
                currZoom = map.getZoom();

                // Get Current Center
                currCenter = map.getCenter();

                // Get all layers
                /*
                currentLayers = map.layers;
                if (currentLayers.length > 0)
                {
                    for (var i=0; i < currentLayers.length; i++)
                    {
                        if ( ! currentLayers[i].isBaseLayer)
                            map.removeLayer(currentLayers[i]);
                    }
                }
                */
                // Refresh Map
                addMarkers(currCat, currStartDate, currEndDate, currZoom, currCenter);
            }
        }


		/**
		 * Refresh Graph on Slider Change
		 */
		function refreshGraph(startDate, endDate)
		{
			var currentCat = gCategoryId;

			// refresh graph
			if (!currentCat || currentCat == '0')
			{
				currentCat = '0';
			}

			var startTime = new Date(startDate * 1000);
			var endTime = new Date(endDate * 1000);

			// daily
			var graphData = "";

			// plot hourly incidents when period is within 2 days
			if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 3)
			{
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=hour", function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 124)
			{
			    // weekly if period > 2 months
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=day", function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			} 
			else if ((endTime - startTime) / (1000 * 60 * 60 * 24) > 124)
			{
				// monthly if period > 4 months
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: currentCat,
						startTime: new Date(startDate * 1000),
					    endTime: new Date(endDate * 1000), mediaType: gMediaType,
						markerOptions: gMarkerOptions,
						graphData: graphData
					});
					gTimeline.plot();
				});
			}

			// Get dailyGraphData for All Categories
			$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat+"?i=day", function(data) {
				dailyGraphData = data[0];
			});

			// Get allGraphData for All Categories
			$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
				allGraphData = data[0];
			});

		}

		/*
		Zoom to Selected Feature from within Popup
		*/
		function zoomToSelectedFeature(lon, lat, zoomfactor)
		{
			var lonlat = new OpenLayers.LonLat(lon,lat);

			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + zoomfactor;
			// Center and Zoom
			map.setCenter(lonlat, newZoom);
			// Remove Popups
			for (var i=0; i<map.popups.length; ++i)
			{
				map.removePopup(map.popups[i]);
			}
		}

		/*
		Add KML/KMZ Layers
		*/
		function switchLayer(layerID, layerURL, layerColor)
		{
			if ( $("#layer_" + layerID).hasClass("active") )
			{
				new_layer = map.getLayersByName("Layer_"+layerID);
				if (new_layer)
				{
					for (var i = 0; i < new_layer.length; i++)
					{
						map.removeLayer(new_layer[i]);
					}
				}
				$("#layer_" + layerID).removeClass("active");

			}
			else
			{
				$("#layer_" + layerID).addClass("active");

				// Get Current Zoom
				currZoom = map.getZoom();

				// Get Current Center
				currCenter = map.getCenter();

				// Add New Layer
				addMarkers('', '', '', currZoom, currCenter, '', layerID, 'layers', layerURL, layerColor);
			}
		}

		/*
		Toggle Layer Switchers
		*/
		function toggleLayer(link, layer){
			if ($("#"+link).text() == "<?php echo Kohana::lang('ui_main.show'); ?>")
			{
				$("#"+link).text("<?php echo Kohana::lang('ui_main.hide'); ?>");
			}
			else
			{
				$("#"+link).text("<?php echo Kohana::lang('ui_main.show'); ?>");
			}
			$('#'+layer).toggle(500);
		}

        /**
         * Adds feature items (contained in vector layers) to the list
         * of feature selection items. This method should be called by all
         * JS functions overlaying data on the main map so that the feature
         * selection control can be used for selection on multiple layers
         */
        function addSelectFeatureItem(featureItem)
        {
            // No layers in feature selection list, safe to push
            if (selectFeatureItems.length == 0)
            {
                selectFeatureItems.push(featureItem);
            }
            else    // Check if layer already exists in feature selection list
            {
                // Name of the layer being added
                var overlayName = featureItem.name;
                var tempArray = [];
                
                // Check if layer exists exists
                for (var i = 0; i < selectFeatureItems.length; i++)
                {
                    item = selectFeatureItems[i];
                    if (item.name != overlayName)
                    {
                        tempArray.push(item);
                    }
                }
                // Recreate the list of layers for the map control
                tempArray.push(featureItem);
                selectFeatureItems = tempArray;
            }

            // Only initialize feature selection control if the map is loaded
            if (mapLoad > 0)
            {
                // Remove all SelectFeature controls from the map
                controls = map.getControlsByClass("OpenLayers.Control.SelectFeature");
                if (selectControl != null & controls.length > 0)
                {
                    for (var i=0; i < controls.length; i++)
                    {
                        map.removeControl(controls[i]);
                    }
                    selectControl = null;
                }

                // Initialize the feature selection control
                // TODO Fix selection of features across different layers
                selectControl = new OpenLayers.Control.SelectFeature(selectFeatureItems);
                map.addControl(selectControl);
                selectControl.activate();
                selectControl.setLayer(selectFeatureItems[0]);
            }
        }
        

		jQuery(function() {
			var map_layer;
			markers = null;
			var catID = '';
			OpenLayers.Strategy.Fixed.prototype.preload=true;
			
			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			- Units in Metres instead of Degrees					
			*/
			var options = {
				units: "mi",
				numZoomLevels: 18,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326,
				eventListeners: {
						"zoomend": mapMove
				    },
				'theme': null
				};
			map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );
			
            <?php echo map::layers_js(FALSE); ?>
            map.addLayers(<?php echo map::layers_array(FALSE); ?>);
			
			// Add Controls
            map.addControl(new OpenLayers.Control.Navigation());
            map.addControl(new OpenLayers.Control.Attribution());
            map.addControl(new OpenLayers.Control.PanZoom());
            map.addControl(new OpenLayers.Control.MousePosition(
            {
                div: document.getElementById('mapMousePosition'),
                numdigits: 5
            }));
            map.addControl(new OpenLayers.Control.Scale('mapScale'));
            map.addControl(new OpenLayers.Control.ScaleLine());
            map.addControl(new OpenLayers.Control.LayerSwitcher());
			
            // display the map projection
            document.getElementById('mapProjection').innerHTML = map.projection;

            gMap = map;
			
			// Category Switch Action
			jQuery("a[id^='cat_']").click(function()
			{
				var catID = this.id.substring(4);
				var catSet = 'cat_' + this.id.substring(4);
				$("a[id^='cat_']").removeClass("active"); // Remove All active
				$("[id^='child_']").hide(); // Hide All Children DIV
				$("#cat_" + catID).addClass("active"); // Add Highlight
				
				currentCat = catID;
				$("#currentCat").val(catID);

				// setUrl not supported with Cluster Strategy
				//markers.setUrl("<?php echo url::site(); ?>" json_url + '/?c=' + catID);
				
				// Destroy any open popups
				onPopupClose();
				
				// Get Current Zoom
				currZoom = map.getZoom();
				
				// Get Current Center
				currCenter = map.getCenter();
				
				gCategoryId = catID;
				var startTime = new Date($("#startDate").val() * 1000);
				var endTime = new Date($("#endDate").val() * 1000);
				addMarkers(catID, $("#startDate").val(), $("#endDate").val(), currZoom, currCenter, gMediaType);
				
				<?php if ($show_timeline): ?>				
				graphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+catID, function(data) {
					graphData = data[0];

					gTimeline = $.timeline({categoryId: catID, startTime: startTime, endTime: endTime,
						graphData: graphData,
						mediaType: gMediaType
					});
					gTimeline.plot();
				});
				
				dailyGraphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+catID+"?i=day", function(data) {
					dailyGraphData = data[0];
				});
				allGraphData = "";
				$.getJSON("<?php echo url::site()."json/timeline/"?>"+currentCat, function(data) {
					allGraphData = data[0];
				});
				<?php endif; ?>

				return false;
			});
			
			// Sharing Layer[s] Switch Action
			jQuery("a[id^='share_']").click(function()
			{
				var shareID = this.id.substring(6);
				
				if ( $("#share_" + shareID).hasClass("active") )
				{
					share_layer = map.getLayersByName("Share_"+shareID);
					if (share_layer)
					{
						for (var i = 0; i < share_layer.length; i++)
						{
							map.removeLayer(share_layer[i]);
						}
					}
					$("#share_" + shareID).removeClass("active");
					
				} 
				else
				{
					$("#share_" + shareID).addClass("active");
					
					// Get Current Zoom
					currZoom = map.getZoom();

					// Get Current Center
					currCenter = map.getCenter();
					
					// Add New Layer
					addMarkers('', '', '', currZoom, currCenter, '', shareID, 'shares');
				}
			});

			// Exit if we don't have any incidents
			if (!jQuery("#startDate").val())
			{
				map.setCenter(new OpenLayers.LonLat(<?php echo $longitude ?>, <?php echo $latitude ?>), 5);
				return;
			}
			
			//Accessible Slider/Select Switch
			jQuery("select#startDate, select#endDate").selectToUISlider({
				labels: 4,
				labelSrc: 'text',
				sliderOptions: {
					change: function(e, ui)
					{
						var startDate = $("#startDate").val();
						var endDate = $("#endDate").val();
						var currentCat = gCategoryId;
						
						// Get Current Category
						currCat = currentCat;
						
						// Get Current Zoom
						currZoom = map.getZoom();
						
						// Get Current Center
						currCenter = map.getCenter();
						
						// If we're in a month date range, switch to
						// non-clustered mode. Default interval is monthly
						var startTime = new Date(startDate * 1000);
						var endTime = new Date(endDate * 1000);
						if ((endTime - startTime) / (1000 * 60 * 60 * 24) <= 32)
						{
							json_url = "json";
						} 
						else
						{
							json_url = default_json_url;
						}
						
						// Refresh Map
						addMarkers(currCat, startDate, endDate, '', '', gMediaType);
						
						refreshGraph(startDate, endDate);
					}
				}
			});
			
			var allGraphData = "";
			var dailyGraphData = "";
			gCategoryId = '0';
			gMediaType = 0;
			
			var startTime = <?php echo $active_startDate ?>;	// Default to most active month
			var endTime = <?php echo $active_endDate ?>;		// Default to most active month
					
			<?php if ($show_timeline): ?>
			// get the closest existing dates in the selection options
			options = jQuery('#startDate > optgroup > option').map(function()
			{
				return $(this).val(); 
			});
			startTime = jQuery.grep(options, function(n,i)
			{
			  return n >= ('' + startTime) ;
			})[0];
			
			options = jQuery('#endDate > optgroup > option').map(function()
			{
				return jQuery(this).val(); 
			});
			endTime = jQuery.grep(options, function(n,i)
			{
			  return n >= ('' + endTime) ;
			})[0];
			
			//$("#startDate").val(startTime);
			//$("#endDate").val(endTime);
			<?php endif; ?>
			
			// Initialize Map
			addMarkers(gCategoryId, startTime, endTime, '', '', gMediaType);
			
			<?php if ($show_timeline): ?>
			refreshGraph(startTime, endTime);
			<?php endif; ?>

			// Media Filter Action
			jQuery('.filters li a').click(function()
			{
				var startTimestamp = $("#startDate").val();
				var endTimestamp = $("#endDate").val();
				var startTime = new Date(startTimestamp * 1000);
				var endTime = new Date(endTimestamp * 1000);
				gMediaType = parseFloat(this.id.replace('media_', '')) || 0;
				
				// Get Current Zoom
				currZoom = map.getZoom();
					
				// Get Current Center
				currCenter = map.getCenter();
				
				// Refresh Map
				addMarkers(currentCat, startTimestamp, endTimestamp, 
				           currZoom, currCenter, gMediaType);
				
				jQuery('.filters li a').attr('class', '');
				jQuery(this).addClass('active');
				gTimeline = jQuery.timeline({categoryId: gCategoryId, startTime: startTime, 
				    endTime: endTime, mediaType: gMediaType,
					url: "<?php echo url::site(); ?>json_url+'/timeline/'"
				});
				gTimeline.plot();
			});
			
			<?php if ($show_timeline): ?>
			jQuery('#playTimeline').click(function()
			{
			    gTimelineMarkers = gTimeline.addMarkers(gStartTime.getTime()/1000,
					$.dayEndDateTime(gEndTime.getTime()/1000), gMap.getZoom(),
					gMap.getCenter(),null,null,null,null,"json");
				gTimeline.playOrPause('raindrops');
			});
			<?php endif; ?>

		});

		// Custom event for Huduma overlays
		<?php Event::run('ushahidi_action.huduma_overlay_js'); ?>