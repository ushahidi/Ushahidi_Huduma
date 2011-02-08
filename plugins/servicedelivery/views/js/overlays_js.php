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
        var overlayURL = "<?php echo url::base().$overlay_json_url; ?>/";

        // Name of the layer for overlaying data on the main map
        var o_layerName = "<?php echo $overlay_layer_name; ?>";

        // Currently selected category
        var categoryId = (currentCat == null)? 0: currentCat;

        /*
         * Overlay Style
         */
        overlayMarkerStyle = function()
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
        };

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
        var m_style = overlayMarkerStyle();

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