<?php
/**
 * Common/shared javascript for rendering content on the maps
 *
 * @author Ushahidi Dev Team - http://www.ushahidi.com
 * @package Huduma - http://github.com/ushahidi/Ushahidi_Huduma
 * @copyright Ushahidi Inc - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
?>
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

		/**
		 * Builds and returns the styling for the overlay markers
		 */
		function getOverlaysMarkerStyle()
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

