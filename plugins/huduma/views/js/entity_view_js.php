<?php
/**
 * Reports view js file.
 *
 * Handles javascript stuff related to entity view function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Entities Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
		var map;
		var myPoint;
		var commentForm;
		
		jQuery(window).load(function() {
			var moved=false;

			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			*/
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			var options = {
				units: "m",
				numZoomLevels: 18,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326
				};
			map = new OpenLayers.Map('map', options);

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

            // Set Feature Styles
            style1 = new OpenLayers.Style({
                pointRadius: "8",
                fillColor: "#ffcc66",
                fillOpacity: "0.7",
                strokeColor: "#CC0000",
                strokeOpacity: "0.7",
                strokeWidth: 4,
                graphicZIndex: 1,
                externalGraphic: "${graphic}",
                graphicOpacity: 1,
                graphicWidth: 21,
                graphicHeight: 25,
                graphicXOffset: -14,
                graphicYOffset: -27
            },
            {
                context:
                {
                    graphic: function(feature)
                    {
                        if ( typeof(feature) != 'undefined' &&
                            feature.data.id == <?php echo $entity_id; ?>)
                        {
                            return "<?php echo url::base().'media/img/openlayers/marker.png' ;?>";
                        }
                        else
                        {
                            return "<?php echo url::base().'media/img/openlayers/marker-gold.png' ;?>";
                        }
                    }
                }
            });
            style2 = new OpenLayers.Style({
                pointRadius: "8",
                fillColor: "#30E900",
                fillOpacity: "0.7",
                strokeColor: "#197700",
                strokeWidth: 3,
                graphicZIndex: 1
            });

            // Create the single marker layer
            markers = new OpenLayers.Layer.GML("<?php echo $entity_name; ?>", "<?php echo url::site().'overlays/single/'.$entity_id; ?>",
            {
				format: OpenLayers.Format.GeoJSON,
				projection: map.displayProjection,
				styleMap: new OpenLayers.StyleMap({"default":style1, "select": style1, "temporary": style2})
            });

            // Add the markers layer
            map.addLayer(markers);
            
			selectCtrl = new OpenLayers.Control.SelectFeature(markers, {
				onSelect: onFeatureSelect,
				onUnselect: onFeatureUnselect
			});
			highlightCtrl = new OpenLayers.Control.SelectFeature(markers, {
			    hover: true,
			    highlightOnly: true,
			    renderIntent: "temporary"
			});

			map.addControl(selectCtrl);
			map.addControl(highlightCtrl);
			selectCtrl.activate();

            // create a lat/lon object
            myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
            myPoint.transform(proj_4326, map.getProjectionObject());

            // display the map centered on a latitude and longitude (Google zoom levels)
            map.setCenter(myPoint, <?php echo ($default_zoom) ? $default_zoom : 10; ?>);


			$("#dialog:ui-dialog").dialog("destroy");

			$("#metadata-dialog").dialog(
				{
					autoOpen: false,
					modal: true,
					width: 600,
					height: 450,
					position: ['center', 100]
				}
			);
        });
        
		function onPopupClose(evt) {
            selectCtrl.unselect(selectedFeature);
        }

        function onFeatureSelect(feature) {
            selectedFeature = feature;
			// Lon/Lat Spherical Mercator
			zoom_point = feature.geometry.getBounds().getCenterLonLat();
			lon = zoom_point.lon;
			lat = zoom_point.lat;
            var content = "<div class=\"infowindow\"><div class=\"infowindow_list\"><ul><li>"+feature.attributes.name + "</li></ul></div>";
			content = content + "\n<div class=\"infowindow_meta\"><a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", 1)'>Zoom&nbsp;In</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='javascript:zoomToSelectedFeature("+ lon + ","+ lat +", -1)'>Zoom&nbsp;Out</a></div>";
			content = content + "</div>";
			// Since KML is user-generated, do naive protection against
            // Javascript.
            if (content.search("<script") != -1) {
                content = "Content contained Javascript! Escaped content below.<br />" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken",
                                     feature.geometry.getBounds().getCenterLonLat(),
                                     new OpenLayers.Size(100,100),
                                     content,
                                     null, true, onPopupClose);
            feature.popup = popup;
            map.addPopup(popup);
        }

        function onFeatureUnselect(feature) {
            map.removePopup(feature.popup);
            feature.popup.destroy();
            feature.popup = null;
        }

		function zoomToSelectedFeature(lon, lat, zoomfactor){
			var lonlat = new OpenLayers.LonLat(lon,lat);
			map.panTo(lonlat);
			// Get Current Zoom
			currZoom = map.getZoom();
			// New Zoom
			newZoom = currZoom + zoomfactor;
			map.zoomTo(newZoom);
		}

		// Display the entity metadata
		function showEntityMetadata() {
			$("#metadata-dialog").dialog( "open" );
		}
		
		/**
		 * Adds the comment form below the comment container referenced by @param containerId
		 */
		function showCommentBox(containerId, commentId) {
		    // Verify specified container exists and commentForm is not null
		    if ($("#"+containerId+"") != null && commentForm == null) {
		        // Fetch and remove the comment form
		        commentForm = $(".dashboard_comment_form");
	        }
	        
		    // Append comment form to the comment container
		    $("#"+containerId+" > div.comment_box_holder").append(commentForm);
		    
		    // Set the comment being replied to
		    $("#dashboard_comment_reply_to").val(unescape(commentId));
		    
		    // Display the cancel button
		    $("#comment_cancel").css("display", "block");
		    $("#comment_cancel").click(function() {
		        // Append comment form to the bottom of the page
		        $("#entity_view_column").append(commentForm);
		        
		        // Hide the current button
		        $(this).hide();
		    });
		}
		