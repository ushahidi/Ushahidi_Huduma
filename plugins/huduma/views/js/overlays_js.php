<?php
/**
 * Create a new Vector (GML) layer on the main map
 *
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     ovelays
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
    /**
     * Function to overlay data on the main map
     * The overlayed data is contained in its own vector graphics layer
     */
    function overlayMainMap() {
        // URL containing the JSON data
        var overlayURL = "<?php echo url::site().$overlay_json_url; ?>/";

        // Name of the layer for overlaying data on the main map
        var o_layerName = "<?php echo $overlay_layer_name; ?>";

        // Currently selected category
        var categoryId = (currentCat == null)? 0: currentCat;


        // Check if the layer contained in o_layerName already exists, remove and redraw
        overlayMarkers = map.getLayersByName(o_layerName);
        
        if (overlayMarkers && overlayMarkers.length > 0)
        {
            for (var i=0; i < overlayMarkers.length; i++)
            {
                map.removeLayer(overlayMarkers[i]);
            }
        }

        // Get the styling for the markers
        var m_style = getOverlaysMarkerStyle();

        // Transform feature point coordinate to Spherical Mercator
        preFeatureInsert = function(feature)
        {
            var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y);
            OpenLayers.Projection.transform(point, proj_4326, proj_900913);
        };
        
        // Set the zoom level
        zoomLevel = (map.getZoom() == 0 || activeZoom == null || activeZoom == '')? defaultZoom : activeZoom;

        // Create the overlay markers
        overlayMarkers = new OpenLayers.Layer.GML(o_layerName, overlayURL + '?z=' + zoomLevel + '&c=' + categoryId,
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
        addSelectFeatureItem(overlayMarkers);
        overlayMarkers.events.on({
            "featureselected": onFeatureSelect,
            "featureunselected": onFeatureUnselect
        });
    }

	jQuery(document).ready(function(){
		jQuery(".cat-stats-graph").horizontalBarGraph({interval: 0.55});
		
		// Event handler for the live search
		$("#livesearch_bar").keyup(function(){
			var search_value = $(this).val();
			if (search_value != null && search_value != '' && search_value.length > 0)
			{
				$.get('<?php echo url::site().'main/live_search/?search='?>'+search_value, 
				function(response){
					if (response.success)
					{
						$("#livesearch_results").css("display", "block");
						$("#livesearch_results").html(response.results);
					}
					else
					{
						$("#livesearch_results").css("display", "none");
					}
				}
				);
			}
			else
			{
				$("#livesearch_results").css("display", "none");
			}
		});
	});
	
	/**
	 * Loads the registration form
	 */
	function showRegistrationForm()
	{
		// Tracks the selected boundary
		var boundary_id = 0;
		
		// Display the dialog
		$("#facebox .content").attr("class", "content body");
		$("#facebox").css("display", "block");
		$("#facebox .content").css("width", "620px");
        
	    $.facebox(function(){
	        jQuery.get('<?php echo url::site().'registration_form'?>',
	            function(data) {
					jQuery.facebox(data);

					//*
					//* EVENT HANDLERS
					//*
					
					// Close form
					$("#close_registration_form").click(function(){
						$.facebox.close();
					})
					
					// Form submission handler
					$("#register_user").click(function(){
						// Fetch the submitted information
						var postData = {
							name : $("#first_name").val() + " " + $("#last_name").val(),
							phone_number : $("#phone_number").val(),
							email : $("#email").val(),
							agency_type_id : $("[name=agency_type_id]").val(),
							category_id : $("#category_id").val(),
							boundary_id : boundary_id,
							static_entity_id: $("#facility_id").val(),
							in_charge : (! $("#in_charge").attr("disabled") && $("#in_charge").attr("checked"))? 1 : 0
						};
        			    
        			    // Post the provided information
        			    $.post('<?php echo url::site().'registration_form/register'; ?>', 
        			        postData,
        			        function(response) {
								if (response.success) {
									// Show message 
									$("#submitStatus").css("display", "block");
									$("#submitStatus").html("<h3><div class=\"green-box\">"+response.message+"</div></h3>");
									setTimeout(function(){ $.facebox.close(); }, 600);
								} else {
									// Generate the error message
									var html_str = "<ul>";
									$.each(response.message, function(key, value){
										html_str += "<li>"+key+": "+value+"</li>";
									});
									html_str += "</ul>";
									
									$("#submitStatus").css("display", "block");
									$("#submitStatus").html("<h3><div class=\"red-box\">"+html_str+"</div></h3>");
								}
        			        }
						);
					}); // END form submission
					
					// Category change - Loads the facility types
					$("#category_id").change(function(){
						$("#facility_id").html("<option value=\"0\">--<?php echo Kohana::lang('ui_huduma.select_facility')?>---</option>");
						$.get('<?php echo url::site().'registration_form/get_facility_types/'?>'+$(this).val(),
							function(data)
							{
								if (data != null && data != "")
								{
									$("#facility_type_id").html(data);
								}
							}
						);
					});
					
					// County selection change
					$("#county_id").change(function(){
						boundary_id = $(this).val();
						if (boundary_id == 0)
						{
							$("#constituency_id").html("<option value=\"0\">---<?php echo Kohana::lang('ui_huduma.select_constituency');?>--</option>");
						}
						else
						{
							$.get('<?php echo url::site().'registration_form/get_constituencies/'?>'+boundary_id,
								function(data)
								{
									if (data != null && data != "")
									{
										$("#constituency_id").html(data);
									}
								}
							);
						}
					});
					
					// Constituency selection change
					$("#constituency_id").change(function(){
						if ($(this).val() != "" && $(this).val() != null)
						{
							// Update boundary id
							boundary_id = $(this).val();
						}
						else
						{
							boundary_id = $("#county_id").val();
						}
					});
					
					// Facility Type change - Loads the facilities
					$("#facility_type_id").change(function(){
						$("#facility_id").html("<option value=\"0\">---<?php echo Kohana::lang('ui_huduma.select_facility'); ?>--</option>");
						$.get('<?php echo url::site().'registration_form/get_facilities/'?>'+$(this).val()+'/'+boundary_id,
							function(data)
							{
								if (data != null && data != "")
								{
									$("#facility_id").html(data);
								}
							}
						);
					});
					
					// Facility selection change
					$("#facility_id").change(function(){
						if ($(this).val() != "" && $(this).val() > 0)
						{
							$("#in_charge").removeAttr("disabled");
						}
					});
					
	            }
	        );
		});
		
	}