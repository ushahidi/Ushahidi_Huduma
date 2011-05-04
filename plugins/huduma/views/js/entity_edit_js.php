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
            map.addControl(new OpenLayers.Control.PanZoom());
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

        });

		// Displays the dialog for adding metadata items
		function showAddMetadataDialog(entity_id) {
			// Display the dialog
			$("#facebox .content").attr("class", "content body");
			$("#facebox").css("display", "block");
			$("#facebox .content").css("width", "650px");

			// Reset item count
			metadataItemCount = 0;
    		
			$.facebox(function(){
				jQuery.get('<?php echo $add_metadata_dialog_url; ?>',
					function(data){
						// Set the dialog data
						jQuery.facebox(data);

						// Event handler for the save button
						$("#save_metadata").click(function(){
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
								$.post('<?php echo  $metadata_update_url; ?>',
										{
											entity_id: unescape(entity_id), metadata_label: metadata_label,
											metadata_value: metadata_value, metadata_as_of_year: metadata_as_of_year,
											action: $("#metadata_form_action").val()
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
												// Show success message
												$("#success_message").html('<div class="green-box"><h4>'+data.message+'</h4></div>');
												$("#success_message").show('fast');
												
												// Close the dialog after 1.5s
												setTimeout(function(){ $.facebox.close(); }, 1500);
											} else {
												// Show error message
												$("#error_message").html('<div class="red-box"><h4>'+data.message+'</h4></div>');
												$("#error_message").show('fast');
											}
            					        },
            					        
            					        "json"
            					        
            					); // End $.post
            					
            				} // End if (metadataCount > 0)
            			
            			}); // End $("#save_metadata").click()
            			
            		}); // End jQuery.get
            	
            }); // End $.facebox()
        
        } // End showAddMetadataDialog


		/**
		 * Adds a section for metadata
		 */
        function addMetadataItem(entity_id) {
            // Incremenent the metadata item counter
            metadataItemCount++;
            $.post('<?php echo $new_metadata_item_url; ?>',
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
		
		// Edit a metadata item in place
		var currentEditRows = [];
		function metadataItemAction(action, itemId, hideLink) {
		    // Get the current row object
		    var editRow = $("#metadata_row_"+itemId);
		    
		    // Handles cancellation of inline edit
		    this.cancel = function(){
		        for (var i = 0; i < currentEditRows.length; i++) {
		            // Get the row id
    	            var rowId = currentEditRows[i].id;
    	            
    	            // Show/Hide links
    	            $("#edit_link_"+rowId).css("display", "block");
    	            $("#delete_link_"+rowId).css("display", "block");
    	            $("#cancel_link_"+rowId).css("display", "none");
    	            $("#save_link_"+rowId).css("display", "none");
    	            
    	            // Remove the inline input fields
    	            $('td.edit_item', currentEditRows[i].editRow).each(function(){
    	                $(this).html($(this).find('input').val());
    	            });
		        }
		    }
		    
		    // Being Processing
		    if (action == 'e') {        // EDIT
		        // Cancel previous edits before proceeding
		        cancel();
		        
    		    // Show/Hide links
    		    $(hideLink).css("display", "none");
    		    $("#delete_link_"+itemId).css("display", "none");
    		    $("#cancel_link_"+itemId).css("display", "block");
    		    $("#save_link_"+itemId).css("display", "block");
    		    
    		    // Show input fields in the table rows
    		    $('td.edit_item', editRow).each(function(){
    		        $(this).html('<input type="text" name="' + $(this).attr('id') + '" value="' + $(this).html() + '" class="text inline">');
    		    });
    		    
    		    // Add current row to the rows collection
    		    currentEditRows.push({ id: itemId, editRow: editRow });
    		    
    		} else if (action == 'd') { // DELETE
    		    var postData = { id : itemId, action : action };
    		    $.post('<?php echo $metadata_update_url; ?>', 
    		            postData,
    		            function(response) {
    		                if (response.success) {
    		                    $(editRow).remove();
    		                } else {
    		                    // TODO: Error handling
    		                }
    		            }
    		    );
    		    
	        } else if (action == 'c') { // CANCEL
	            
	            cancel();
	            
	        } else if (action == 's') { // SAVE
	            
	            // Data for posting
	            var postData = {
	                id : itemId,
	                action : action,
	                item_label : $('#item_label_'+itemId, editRow).find('input').val(),
	                item_value : $('#item_value_'+itemId, editRow).find('input').val(),
	                as_of_year : $('#as_of_year_'+itemId, editRow).find('input').val()
	            }
	            
	            // Submit metadata for update
	            $.post('<?php echo $metadata_update_url; ?>',
	                    postData,
	                    function(response) {
	                        if (response.success) {
                	            $('td.edit_item', editRow).each(function(){
                	                $(this).html($(this).find('input').val());
                	            });
                	            
                	            // Show/Hide Links
                	            $("#edit_link_"+itemId).css("display", "block");
                	            $("#delete_link_"+itemId).css("display", "block");
                    		    $("#cancel_link_"+itemId).css("display", "none");
                	            $(hideLink).css("display", "none");
                	            
        	                } else {
        	                    // TODO: Error handling
        	                }
	                    }
	            );
	        }
		}