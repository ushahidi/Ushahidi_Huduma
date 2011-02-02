<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Service delivery plugin hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Service Delivery plugin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class servicedelivery
{
    private $table_prefix; // Table prefix

    /**
     * Registers the main event method - add
     */
    public function __construct()
    {
        // Set Table Prefix
        $this->table_prefix = Kohana::config('database.default.table_prefix');

        Event::add('system.pre_controller', array($this, 'add'));
    }

    /**
     * Adds all the events to the Ushahidi main application
     */
    public function add()
    {
        Event::add('ushahidi_action.nav_main_right_tabs', array($this, 'generate_service_delivery_tab'));

        // Check if the current controller is the json controller; generates markers for the main map
        if (Router::$controller == 'json')
        {
            // TODO Check if entity clustering is enabled
            Event::add('ushahidi_action.markers_main_map_cluster', array($this, 'generate_cluster_markers'));
        }

    }

    /**
     * Adds a service delivery menu to the list of admin menus on the right of the admin console
     */
    public function generate_service_delivery_tab()
    {
        // Get the current event data
        $main_right_tabs = Event::$data;

        // Generate the menu string
        $main_right_tabs = arr::merge($main_right_tabs, array(
            'servicedelivery' => Kohana::lang('ui_servicedelivery.servicedelivery'))
        );
        
        // Set the event data
        Event::$data = $main_right_tabs;
    }

    /**
     * Generates cluster markers for the static entities
     */
    public function generate_cluster_markers()
    {
        // Database instance
        $db = new Database();


        // TODO Get/set the color for the static entities from the plugin settings
        $color = '31576b';

        // TODO Get entity id from the URL variable data
        $entity_id = 0;
        
        // Get the  current zoom level
        $zoomLevel = (isset($_GET['z']) AND !empty($_GET['z'])) ?
            (int) $_GET['z'] : 8;

        // Define the cluster radius, $distance, as a function of the zoom value
        $distance = (10000000 >> $zoomLevel) / 100000;
        
        // For adding predicates to the WHERE clause
        $filter = "";

        // Get the selected category and filter by entities for that category
        $category_id = (isset($_GET['c']) AND !empty($_GET['c']) &&
            is_numeric($_GET['c']) AND $_GET['c'] != 0) ?
            (int) $_GET['c'] : 0;

        $filter = ($category_id > 0)? ' AND se.category_id = '.$category_id : '';


        // Build SQL to fetch the entities from the database
        $sql = 'SELECT e.id, e.static_entity_type_id, se.category_id, e.entity_name, e.latitude, e.longitude ';
        $sql .= 'FROM '.$this->table_prefix.'static_entity e ';
        $sql .= 'INNER JOIN '.$this->table_prefix.'static_entity_type se ON (e.static_entity_type_id = se.id) ';
        $sql .= 'WHERE 1=1 ';
        $sql .= $filter;

        // Execute query
        $entities = $db->query($sql);

        $entities = $entities->result_array(FALSE);

        // Create markers for the entities
        $markers = array();
        foreach ($entities as $entity)
        {
            $markers[] = array(
                'id' => $entity['id'],
                'entity_name' => $entity['entity_name'],
                'latitude' => $entity['latitude'],
                'longitude' => $entity['longitude'],
                'static_entity_type_id' => $entity['static_entity_type_id'],
                'category_id' => $entity['category_id']
            );
        }

        $clusters = array();    // Clustered
        $singles = array();     // Non clustered
        
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
        }

        // Generate the JSON for the clusters
        $json_array = array();
        $json_item = "";
        $icon = "";
        foreach ($clusters as $cluster)
        {
            // Calculate the cluster center
            $bounds = $this->_calculate_cluster_center($cluster);
            $cluster_center = $bounds['center'];
//            $southwest = $bounds['sw'];
//            $northeast = $bounds['ne'];

            // Number of items in cluster
            $cluster_count = count($cluster);
            $cluster_info = $this->_get_entity_cluster_info($cluster, $bounds);
            $json_item = "{";
            $json_item .= "\"type\":\"Feature\",";
            $json_item .= "\"properties\": {";
            $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', $cluster_info))."\",";
//            $json_item .= "\"link\": \"".url::base()."entities/index/?c=".$entity_id."&sw=".$southwest."&ne=".$northeast."\", ";
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

        Event::$data = array_merge(Event::$data, $json_array);
    }

    /**
     * Calculates the center point of a cluster
     * 
     * @param array $cluster
     */
    private function _calculate_cluster_center($cluster)
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
     */
    private function _get_entity_cluster_info($cluster, $bounds)
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
            $info_html .= count($category_data) > 0 ? "<table class='popup-table'>" : "";

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

new servicedelivery();