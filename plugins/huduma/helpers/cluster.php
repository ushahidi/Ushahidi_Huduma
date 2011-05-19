<?php
/* 
 * Clustering helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     cluster helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class cluster_Core {
	
	/**
	 * Clusters the entities for a specific boundary and returns a JSON string
	 *
	 * @param int boundary_id Boundary for the entities to be clustered
	 * @param int $zoom Current zoom level on the map
	 * @return string
	 */
	public static function get_clustered_entities($boundary_id, $zoom)
	{
		// Calculate the cluster distance
		$distance = (10000000 >> $zoom) / 100000;
		$color = '31576b';
		
		// Fetch the entities
		$entities = Boundary_Model::get_entities($boundary_id);
		$markers = array();
		foreach ($entities as $entity)
		{
			$markers[] = array(
				'id' => $entity->id,
				'entity_name' => $entity->entity_name,
				'latitude' => $entity->latitude,
				'longitude' => $entity->longitude,
				'static_entity_type_id' => $entity->static_entity_type_id,
				'category_id' => $entity->static_entity_type->category_id
			);
		}
		
		$clusters = array(); // To hold clustered items
		$singles = array(); // To hold single items - those not in a cluster
		
		// Compare each marker to every other
		while (count($markers))
		{
			$marker = array_pop($markers);
			$cluster = array();

			// Compare $maker to each of the elements in $markers
			foreach ($markers as $key => $target)
			{
				// Get the distance of the markets from each other - K-means clustering
				$pixels = abs($marker['latitude'] - $target['latitude']) + abs($marker['longitude'] - $target['longitude']);

				// Check if difference is within the cluster radius
				if ($pixels < $distance) // $target in the same radius as $marker
				{
					// Invalidate the value of $target in the $markers array
					unset($markers[$key]);
					$target['distance'] = $pixels;

					// Add $target to the cluster
					$cluster[] = $target;
				}
			}

			// If an item was added to the cluster, also add the $marker item
			if (count($cluster) > 0)
			{
				$cluster[] = $marker;
				$clusters[] = $cluster;
			}
			else // $marker is an outlier, add to $singles
			{
				$singles[] = $marker;
			}
		} // END while
		
		// Logging
		Kohana::log('info', sprintf('Generated %d clusters at zoom level %d', count($clusters), $zoom));
		
		// Generate the JSON for the clusters
		$json_array = array();
		$json_item = "";
		$icon = "";
		
		foreach ($clusters as $cluster)
		{
			// Calculate the cluster center
			$bounds = self::calculate_cluster_center($cluster);
			$cluster_center = $bounds['center'];

			// Number of items in cluster
			$cluster_count = count($cluster);
			$cluster_info = self::get_entity_cluster_info($cluster, $bounds);
			$json_item = "{";
			$json_item .= "\"type\":\"Feature\",";
			$json_item .= "\"properties\": {";
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', $cluster_info))."\",";
			$json_item .= "\"entity\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
			$json_item .= "\"timestamp\": \"0\", ";
			$json_item .= "\"count\": \"" . $cluster_count . "\"";
			$json_item .= "},";
			$json_item .= "\"geometry\": {";
			$json_item .= "\"type\":\"Point\", ";
			$json_item .= "\"coordinates\":[" . $cluster_center . "]";
			$json_item .= "}";
			$json_item .= "}";

			array_push($json_array, $json_item);
		}

		foreach ($singles as $single)
		{
			$json_item = "{";
			$json_item .= "\"type\":\"Feature\",";
			$json_item .= "\"properties\": {";
			$json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href=" . url::base() . "entities/view/" . $single['id'] . "/>".str_replace('"','\"',$single['entity_name'])."</a>")) . "\",";
			$json_item .= "\"link\": \"".url::base()."entities/view/".$single['id']."\", ";
			$json_item .= "\"entity\":[0], ";
			$json_item .= "\"color\": \"".$color."\", ";
			$json_item .= "\"icon\": \"".$icon."\", ";
			$json_item .= "\"timestamp\": \"0\", ";
			$json_item .= "\"count\": \"" . 1 . "\"";
			$json_item .= "},";
			$json_item .= "\"geometry\": {";
			$json_item .= "\"type\":\"Point\", ";
			$json_item .= "\"coordinates\":[" . $single['longitude'] . ", " . $single['latitude'] . "]";
			$json_item .= "}";
			$json_item .= "}";

			array_push($json_array, $json_item);
		}
		return implode(",", $json_array);
	}
	
	/**
	 * Calculates the center point of a cluster
	 *
	 * @param array $cluster
	 */
	public static function calculate_cluster_center($cluster)
	{
        // Calculate average lat and lon of clustered items
        $south = 0;
        $west = 0;
        $north = 0;
        $east = 0;

        $lat_sum = $lon_sum = 0;
        foreach ($cluster as $marker)
        {
            if (!$south)
            {
                $south = $marker['latitude'];
            }
            elseif ($marker['latitude'] < $south)
            {
                $south = $marker['latitude'];
            }

            if (!$west)
            {
                $west = $marker['longitude'];
            }
            elseif ($marker['longitude'] < $west)
            {
                $west = $marker['longitude'];
            }

            if (!$north)
            {
                $north = $marker['latitude'];
            }
            elseif ($marker['latitude'] > $north)
            {
                $north = $marker['latitude'];
            }

            if (!$east)
            {
                $east = $marker['longitude'];
            }
            elseif ($marker['longitude'] > $east)
            {
                $east = $marker['longitude'];
            }

            $lat_sum += $marker['latitude'];
            $lon_sum += $marker['longitude'];
        }

        $lat_avg = $lat_sum / count($cluster);
        $lon_avg = $lon_sum / count($cluster);

        $center = $lon_avg.",".$lat_avg;
        $sw = $west.",".$south;
        $ne = $east.",".$north;

        return array(
            "center"=>$center,
            "sw"=>$sw,
            "ne"=>$ne
        );		
	}
	
	/**
	 * Gets the following information about an entities cluster
	 *  - No. of entities items in the cluster
	 *  - Distribution of the items i.e. per category, per entity type
	 *
	 * @param array $cluster
	 * @param array $bounds
	 * @return $string
	 */
	public static function get_entity_cluster_info($cluster, $bounds)
	{
        $item_count = count($cluster);
        $categories = array();

        foreach ($cluster as $cluster_item)
        {
            // Get the category id
            $category_id  = $cluster_item['category_id'];

            if (! isset($categories[$category_id]))
            {
                $categories[$category_id] = array();
            }

            // Get entity type
            $type_id = $cluster_item['static_entity_type_id'];

            // Check if the entity exists in the catgegory
            if ( ! isset($categories[$category_id][$type_id]))
            {
                $categories[$category_id][$type_id] = 1;
            }
            else
            {
                $categories[$category_id][$type_id]++;
            }

        }

        // Get the sw and ne bounds
        $southwest = $bounds['sw'];
        $northeast = $bounds['ne'];

        // Create the cluster info HTML
        $info_html = "<h5>".$item_count." Entities </h5>";

        foreach ($categories as $category => $category_data)
        {
            // Display the category icon
            $category_model = ORM::factory('category', $category);

            $category_image = $category_model->category_image;
            $category_color = $category_model->category_color;

            // URL for filtering the entities by category
            $category_url = url::base().'entities/index/?c='.$category.'&sw='.$southwest.'&ne='.$northeast;

            $info_html .= "<div>";
            if ( ! empty($category_image))
            {
                $image_url = url::base().Kohana::config('upload.relative_directory')."/".$category_model->category_image_thumb;
                $info_html .= "<img src='".$image_url."'/>";
            }
            else
            {
                $swatch_url = url::base()."swatch/?c=".$category_color."&w=16&h=16";
                $info_html .= "<img src='".$swatch_url."' border='0' />";
            }

            $info_html .= "<span style='padding-left:3px; position:relative; top:-3px; font-weight:bold;'>";
            $info_html .= "<a href='".$category_url."'>".$category_model->category_title."</a>";
            $info_html .= "</span>";
            $info_html .= "</div>";

            // Breakdown table for each categegory
            $info_html .= count($category_data) > 0 ? "<table class='popup-table' width='100%'>" : "";

            foreach ($category_data as $key => $value)
            {
                $entity_type = ORM::factory('static_entity_type', $key);

                // Construct the entity type URL - for filtering entities by type
                $entity_type_url = url::base().'entities/index/?e='.$key.'&sw='.$southwest.'&ne='.$northeast;

                $info_html .= "<tr>";
                $info_html .= "<td><a href='".$entity_type_url."'>". $entity_type->type_name ."</a></td>";
                $info_html .= "<td align='right'><strong>". $value ."</strong></td>";
                $info_html .= "</tr>";
            }

            $info_html .= count($category_data) > 0 ? "</table><br/>" : "";
        }

        // Destroy the $categories reference
        unset($categories);

        return $info_html;		
	}
	
}
?>