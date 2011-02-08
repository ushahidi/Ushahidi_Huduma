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
		jQuery(window).load(function() {
			var moved=false;

			/*
			- Initialize Map
			- Uses Spherical Mercator Projection
			*/
			var proj_4326 = new OpenLayers.Projection('EPSG:4326');
			var proj_900913 = new OpenLayers.Projection('EPSG:900913');
			var options = {
				units: "dd",
				numZoomLevels: 18,
				controls:[],
				projection: proj_900913,
				'displayProjection': proj_4326
				};
			map = new OpenLayers.Map('map', options);
			map.addControl( new OpenLayers.Control.LoadingPanel({minSize: new OpenLayers.Size(573, 366)}) );

			<?php echo map::layers_js(FALSE); ?>
            map.addLayers(<?php echo map::layers_array(FALSE); ?>);
            map.addControl(new OpenLayers.Control.Navigation());
            map.addControl(new OpenLayers.Control.PanZoomBar());
            map.addControl(new OpenLayers.Control.MousePosition(
				{ div: 	document.getElementById('mapMousePosition'), numdigits: 5
			}));
                
            map.addControl(new OpenLayers.Control.Scale('mapScale'));
            map.addControl(new OpenLayers.Control.ScaleLine());
            map.addControl(new OpenLayers.Control.LayerSwitcher());

            // Create the single marker layer
            markers = new OpenLayers.Layer.Markers("<?php echo $entity_name; ?>");

            // Add the markers layer
            map.addLayer(markers);

            // create a lat/lon object
            myPoint = new OpenLayers.LonLat(<?php echo $longitude; ?>, <?php echo $latitude; ?>);
            myPoint.transform(proj_4326, map.getProjectionObject());

            // Create a maker object for the lat/lon
            marker = new OpenLayers.Marker(myPoint, 
                    new OpenLayers.Icon("<?php echo url::base().'media/img/ico-03.png'; ?>", new OpenLayers.Size(15,23))
                );
            markers.addMarker(marker);

            // display the map centered on a latitude and longitude (Google zoom levels)
            map.setCenter(myPoint, <?php echo ($default_zoom) ? $default_zoom : 10; ?>);
        });

        // TODO Add validation JS for comment fields
        
        jQuery(window).bind("load", function() {
            jQuery("div#slider1").codaSlider()
            // jQuery("div#slider2").codaSlider()
            // etc, etc. Beware of cross-linking difficulties if using multiple sliders on one page.
        });
