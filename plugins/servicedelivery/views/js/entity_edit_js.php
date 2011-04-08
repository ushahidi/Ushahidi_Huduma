<?php
/**
 * Edit service providers js file.
 *
 * Handles javascript stuff related to edit report function.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     ServiceProviders Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
        // Map variables
        var map;
        var thisLayer;
        var proj_4326 = new OpenLayers.Projection('EPSG:4326');
        var proj_900913 = new OpenLayers.Projection('EPSG:900913');
        var markers;
		var screenWidth;
		var dialogWidth = 650;
		var metadataItemCount = 0;

        $(document).ready(function() {
			// Set the screen width
			screenWidth = $(this).width();
			
            // Now initialise the map
            var options = {
            units: "m"
            , numZoomLevels: 16
            , controls:[],
            projection: proj_900913,
            'displayProjection': proj_4326
            };
            map = new OpenLayers.Map('divMap', options);

            <?php echo map::layers_js(FALSE); ?>
            map.addLayers(<?php echo map::layers_array(FALSE); ?>);

            map.addControl(new OpenLayers.Control.Navigation());
            map.addControl(new OpenLayers.Control.PanZoomBar());
            map.addControl(new OpenLayers.Control.MousePosition());
            map.addControl(new OpenLayers.Control.LayerSwitcher());

            // Create the markers layer
            markers = new OpenLayers.Layer.Markers("Markers");
            map.addLayer(markers);

            // create a lat/lon object
            var myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
            myPoint.transform(proj_4326, map.getProjectionObject());

            // create a marker positioned at a lon/lat
            var marker = new OpenLayers.Marker(myPoint);
            markers.addMarker(marker);

            // display the map centered on a latitude and longitude (Google zoom levels)
            map.setCenter(myPoint, <?php echo $default_zoom; ?>);

            // Detect Map Clicks
            map.events.register("click", map, function(e){
                var lonlat = map.getLonLatFromViewPortPx(e.xy);
                var lonlat2 = map.getLonLatFromViewPortPx(e.xy);
                m = new OpenLayers.Marker(lonlat);
                markers.clearMarkers();
                markers.addMarker(m);

                lonlat2.transform(proj_900913,proj_4326);
                // Update form values (jQuery)
                $("#latitude").attr("value", lonlat2.lat);
                $("#longitude").attr("value", lonlat2.lon);
            });

            // GeoCode
            /*
            $('.btn_find').live('click', function () {
                geoCode();
            });
            $('#location_find').bind('keypress', function(e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if(code == 13) { //Enter keycode
                    geoCode();
                    return false;
                }
            });
            */

            // Event on Latitude/Longitude Typing Change
            $('#latitude, #longitude').bind("change keyup", function() {
                var newlat = $("#latitude").val();
                var newlon = $("#longitude").val();
                if (!isNaN(newlat) && !isNaN(newlon))
                {
                    var lonlat = new OpenLayers.LonLat(newlon, newlat);
                    lonlat.transform(proj_4326,proj_900913);
                    m = new OpenLayers.Marker(lonlat);
                    markers.clearMarkers();
                    markers.addMarker(m);
                    map.setCenter(lonlat, <?php echo $default_zoom; ?>);
                }
                else
                {
                    alert('Invalid value!')
                }
            });

            /* Form Actions */
            // Action on Save Only
            $('.btn_save').live('click', function () {
                $("#save").attr("value", "1");
                $(this).parents("form").submit();
                return false;
            });

            $('.btn_save_close').live('click', function () {
                $(this).parents("form").submit();
                return false;
            });

            // Delete Action
            $('.btn_delete').live('click', function () {
                var agree=confirm("<?php echo Kohana::lang('ui_admin.are_you_sure_you_want_to'); ?> <?php echo Kohana::lang('ui_admin.delete_action'); ?>?");
                if (agree){
                    $('#entityMain').submit();
                }
                return false;
            });

			// Hide the div for adding a new metadata item
			$("#metadata_item_new").css('visibility', 'hidden');

			// Disable the dialog from being shown
			$("#dialog").css("visibility", "hidden");
        });

		// Displays the dialog for adding metadata items
		function showAddMetadataDialog(entity_id) {
			$("#dialog").css("visibility", "visible");

			// Remove the dialog functionality completely
			$("#dialog").dialog("destroy");

			// Calculate the center position
			var centerPos = new Number((screenWidth - dialogWidth) / 2).toFixed();

			// Show the dialog
			$("#dialog").dialog({
				modal: true,
				width: dialogWidth,
				position: [centerPos, 100],
				buttons: [
					{
						text: "<?php echo Kohana::lang('ui_main.save'); ?>",
						click: function() {

							// Only post if the metadata count > 0
							if (metadataItemCount > 0) {
								// To hold the metadata items
								var metadata_label = [];
								var metadata_value = [];
								var metadata_as_of_year = [];

								for (i=1; i<=metadataItemCount; i++) {
									metadata_label.push($("#metadata_label_" + i).val());
									metadata_value.push($("#metadata_value_" + i).val());
									metadata_as_of_year.push($("#metadata_as_of_year_" + i).val());
								}

								// Save an items that may be on the dialog
								$.post(
										'<?php echo  url::site() . 'admin/entities/metadata_save'; ?>',

										{
											entity_id: unescape(entity_id), metadata_label: metadata_label,
											metadata_value: metadata_value, metadata_as_of_year: metadata_as_of_year
										},

										function(data) {
											if (data.status) {
												// Display the newly added items on the edit page
												if ($("#metadata-list") != null) {
													// To hold the HTML of the newly added items
													var items_html = '';

													$.each(data.metadata, function(item, i){
														items_html += '<tr>';
														items_html += '<td>'+this.label+'</td>';
														items_html += '<td>'+this.value+'</td>';
														items_html += '<td>'+this.as_of_year+'</td>';
														items_html += '</tr>'
													});

													$("#metadata-list tr:last").after(items_html);
												}
											} else {
												// Show error message
												alert(data.message);
											}
										},

										"json"
								);
							}

							// Close the dialog
							$(this).dialog("close");
						}
					},
					{
						text: "<?php echo Kohana::lang('ui_admin.cancel'); ?>",
						click: function(){ $(this).dialog("close"); }
					}
				]
			});

			// Attach event handlers
			$("#dialog").dialog({
				close: function(){
						// Remove all previously added metadata items
						// TODO: Only remove the items not saved
						$("#metadata_item_new").html('');

						// Reset the metadata item counter
						metadataItemCount = 0;
					}
				}
			);
		}


		/**
		 * Adds a section for metadata
		 */
		function addMetadataItem(entity_id) {
			// Incremenent the metadata item counter
			metadataItemCount++;
			$.post(
					'<?php echo url::site() . 'admin/entities/metadata_new' ?>',
					
					{ entity_id: unescape(entity_id), item_id: metadataItemCount },

					function(data){
						if (data.status) {
							$("#metadata_item_new").css('visibility', 'visible');
							$("#metadata_item_new").append(data.response);

							setTimeout('$("#metadata_label_"+'+metadataItemCount+').focus()', 300);
						} else {
							metadataItemCounter--;
							alert(data.message);
						}
					},

					"json"
			);
			
		}