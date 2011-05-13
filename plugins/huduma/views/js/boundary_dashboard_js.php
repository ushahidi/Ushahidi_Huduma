<?php
/**
 * PHP Javascript file for the boundary-level dashboard landing page
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Home Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
			<?php require SYSPATH.'../plugins/huduma/views/js/dashboard_common_js.php' ?>
			var map; // To hold the map
			var point; // To hold the map centre
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			var markerRadius = <?php echo $marker_radius; ?>;
			var markerOpacity = "<?php echo $marker_opacity; ?>";
			var mapLoad = 0;
			
			// When the page loads
			$(document).ready(function() {
				var moved=false;

				/*
				- Initialize Map
				- Uses Spherical Mercator Projection
				*/
				var options = {
					units: "m",
					numZoomLevels: 18,
					controls:[],
					projection: proj_900913,
					'displayProjection': proj_4326,
					eventListeners: {
						"zoomend": mapZoomed 
					}
				};
				map = new OpenLayers.Map('divMap', options);

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
				
				<?php if ($layer_file_url): ?>
				// Get vector layer
				boundaryLayer = getBoundaryLayer('<?php echo $layer_name; ?>', '<?php echo $layer_color; ?>');
				
				// Load the boundary layer file
				$.get('<?php echo $layer_file_url; ?>', function(data) {
					layerVectors = new OpenLayers.Format.GeoJSON().read(data);
					layerFeature = layerVectors[0];
					layerFeature.geometry.transform(proj_4326, proj_900913);
					
					// Get the centre point
					centroid = layerFeature.geometry.getCentroid();
					lonLat = new OpenLayers.LonLat(centroid.x, centroid.y);
					
					boundaryLayer.addFeatures(layerFeature);
					map.addLayer(boundaryLayer);
					map.setCenter(lonLat, <?php echo $default_zoom; ?>);
					mapLoad += 1;
				});
				<?php endif; ?>
				
				// Add the overlay for the entities - Add a delay so that the items
				// are rendered on top of the boundary layer
				setTimeout(function() { addEntitiesOverlay(); }, 1500);
				
				// Graphing JS
				$(".dash_bar_graphs").horizontalBarGraph({interval: 1});
				
			});
			
			// Callback function to be executed when the map is done zooming
			function mapZoomed() {
				if (mapLoad > 0) {
					addEntitiesOverlay();
				}
			}
			
			// Creates and returns an OpenLayers.Layer.Vector object including sytling (for the layer)
			function getBoundaryLayer(layerName, layerColor) {
				//> Stlying for the GeoJSON layer
				context = {
					getColor: function(feature) {
						var f = feature;
						f.attributes.color = "#"+layerColor;
						feature = f;

						return feature.attributes["color"];
					}
				};

				template = {
					fillOpacity: 0.35,
					strokeColor: "#888888",
					strokeWidth: 2,
					fillColor: "${getColor}"
				};

				// Layer style
				layerStyle = new OpenLayers.StyleMap( { 'default': new OpenLayers.Style(template, {context: context}) });
				vLayer = new OpenLayers.Layer.Vector(layerName, {projection: proj_4326, styleMap: layerStyle});

				return vLayer;
			}
			
			// Adds the layer for the static entities
			function addEntitiesOverlay() {
				// Delete the layer if it exists
				layers = map.getLayersByName("Facilities");
				if (layers && layers.length > 0) {
					for (var i=0; i<layers.length; i++) {
						map.removeLayer(layers[i]);
					}
				}
				
				// Get the styling for the markers
				dashboardMarkerStyle = getMarkerStyle();
				
				// Transform feature point coordinate to Spherical Mercator
				preFeatureInsert = function(feature)
				{
					var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
					OpenLayers.Projection.transform(point, proj_4326, proj_900913);
				};
		
				zoomLevel = (map.getZoom() > 0) ? map.getZoom() : <?php echo $default_zoom; ?>;
				overlayURL = '<?php echo sprintf(url::site().'dashboards/home/boundary_entities/?boundary_id=%d',$boundary_id); ?>';
				entitiesLayer = new OpenLayers.Layer.GML("Facilities", overlayURL+'&zoom='+zoomLevel, 
					{
						preFeatureInsert: preFeatureInsert,
						format: OpenLayers.Format.GeoJSON,
						projection: proj_4326,
						formatOptions: {
							extractStyles: true,
							extractAttributes: true,
						},
						styleMap: new OpenLayers.StyleMap({
							"default": dashboardMarkerStyle,
							"select": dashboardMarkerStyle 
						})
					}
				);
				
				map.addLayer(entitiesLayer);
				
				// TODO: Add feature selection events for entities
			}
			
			/*
			 - Gets the style for the entity markers
			 */
			function getMarkerStyle() {
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
	                            if($.browser.msie && $.browser.version=="6.0")
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