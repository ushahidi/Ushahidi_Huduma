<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Overlays Controller for clustering static entity data on top
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Overlays Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Overlays_Controller extends Template_Controller {

    public $auto_render = TRUE;

    // Main Template
    public $template = 'json';

    // Table preifx
    protected $table_prefix;

    /**
     * Constructor
     */
    public function  __construct()
    {
        parent::__construct();

        // Set the table prefix
        $this->table_prefix = Kohana::config('database.default.table_prefix');

        // Cacheable JSON controller
        $this->is_cacheable = TRUE;
    }

    public function single($entity_id = 0)
    {
        $json = "";
        $json_item = "";
        $json_array = array();

        $marker = ORM::factory('static_entity', $entity_id);

        // Check if the marker exists and belongs to a visible category
        if ($marker->loaded AND $marker->static_entity_type->category->category_visible == 1)
        {
            // Get all neighbouring entities
            $latitude = $marker->latitude;
            $longitude = $marker->longitude;
            $entity_type_id = $marker->static_entity_type_id;

            // Database
            $db = new Database();

            // Get neighbouring entities within 50KM
            $sql = "SELECT DISTINCT e.id, e.entity_name, e.`latitude`, e.`longitude`, ";;
            $sql .= "((ACOS(SIN($latitude * PI() / 180) * SIN(e.`latitude` * PI() / 180) + COS($latitude * PI() / 180) * COS(e.`latitude` * PI() / 180) * COS(($longitude - e.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance ";
            $sql .= "FROM `".$this->table_prefix."static_entity` e ";
            $sql .= "WHERE e.static_entity_type_id = ".$entity_type_id." ";
            $sql .= "HAVING distance <= '10' ";
            $sql .= "ORDER BY e.`entity_name` DESC LIMIT 100 ";

            /**
             *  // Leave out display of neighbouring entities for now
            $rows = $db->query($sql);
            
            foreach ($rows as $row)
            {
                $json_item = "{";
                $json_item .= "\"type\":\"Feature\",";
                $json_item .= "\"properties\": {";
                $json_item .= "\"id\": \"".$row->id."\", ";

				$encoded_name = utf8tohtml::convert($row->entity_name,TRUE);
				$encoded_name = str_ireplace('"','&#34;',$encoded_name);
				$encoded_name = json_encode($encoded_name);
				$encoded_name = str_ireplace('"','',$encoded_name);

                $json_item .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "entities/view/" . $row->id . "'>".$encoded_name."</a>")) . "\",";
                $json_item .= "\"link\": \"".url::base()."entities/view/".$row->id."\" ";
                $json_item .= "},";
                $json_item .= "\"geometry\": {";
                $json_item .= "\"type\":\"Point\", ";
                $json_item .= "\"coordinates\":[" . $row->longitude . ", " . $row->latitude . "]";
                $json_item .= "}";
                $json_item .= "}";

                array_push($json_array, $json_item);
            }
             */
            
			// Single Main Entity
			$json_single = "{";
			$json_single .= "\"type\":\"Feature\",";
			$json_single .= "\"properties\": {";
			$json_single .= "\"id\": \"".$marker->id."\", ";

			$encoded_name = utf8tohtml::convert($marker->entity_name,TRUE);

			$json_single .= "\"name\":\"" . str_replace(chr(10), ' ', str_replace(chr(13), ' ', "<a href='" . url::base() . "entities/view/" . $marker->id . "'>".$encoded_name."</a>")) . "\",";
			$json_single .= "\"link\": \"".url::base()."entities/view/".$marker->id."\" ";
			$json_single .= "},";
            $json_single .= "\"geometry\": {";
            $json_single .= "\"type\":\"Point\", ";
            $json_single .= "\"coordinates\":[" . $marker->longitude . ", " . $marker->latitude . "]";
            $json_single .= "}";
            $json_single .= "}";

            array_push($json_array, $json_single);
        }
        
        $json = implode(",", $json_array);

        header('Content-type: application/json; charset=utf-8');
        $this->template->json = $json;
    }

    /**
     * Generates JSON in cluster mode
     */
    public function cluster()
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

        // Check if the category id is non zero, change the color to match that of the category
        if ($category_id > 0)
        {
            $color = ORM::factory('category', $category_id)->category_color;
        }

        $filter = ($category_id > 0)? ' AND se.category_id = '.$category_id : '';


        // Build SQL to fetch the entities from the database
        $sql = 'SELECT e.id, e.static_entity_type_id, se.category_id, e.entity_name, e.latitude, e.longitude ';
        $sql .= 'FROM '.$this->table_prefix.'static_entity e ';
        $sql .= 'INNER JOIN '.$this->table_prefix.'static_entity_type se ON (e.static_entity_type_id = se.id) ';
        $sql .= 'INNER JOIN '.$this->table_prefix.'category c ON (se.category_id = c.id) ';
        $sql .= 'WHERE c.category_visible = 1 ';
        $sql .= 'AND 1=1 ';
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
            $bounds = cluster::calculate_cluster_center($cluster);
            $cluster_center = $bounds['center'];

            // Number of items in cluster
            $cluster_count = count($cluster);
            $cluster_info = cluster::get_entity_cluster_info($cluster, $bounds);
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

        $json = implode(",", $json_array);

        header('Content-type: application/json; charst=utf-8');
        $this->template->json = $json;

    }
    
}