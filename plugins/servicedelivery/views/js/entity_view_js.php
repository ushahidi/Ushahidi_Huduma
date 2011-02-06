<?php
/**
 * Single entity view js file.
 *
 * Handles javascript stuff related to edit report function.
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
        // Map variables
        var map;
        var thisLayer;
        var proj_4326 = new OpenLayers.Projection('EPSG:4326');
        var proj_900913 = new OpenLayers.Projection('EPSG:900913');
        var markers;

        $(document).ready(function() {
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
           /* map.events.register("click", map, function(e){
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
			*/
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
        });
