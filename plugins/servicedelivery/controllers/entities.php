<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Entities Controller for the frontend
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
class Entities_Controller extends Frontend_Controller {
    var $logged_in;

    public function index()
    {
        // Cacheable controller
        $this->is_cachable = TRUE;

        $this->template->header->this_page = 'entities';
        $this->template->content = new View('frontend/entities');

        // Database instance
//        $db = new Database;

        // Get entity type ids if we're allowed to filter by category
        $entity_type_ids = array();
        if (isset($_GET['e']) AND !empty($_GET['e']) AND $_GET['e'] != 0)
        {
            $static_entity_type_id = (int)$_GET['e'];

            array_push($entity_type_ids, $static_entity_type_id);
        }

        // Check if the category id has been set
        if (isset($_GET['c']) AND !empty($_GET['c']) AND $_GET['c'] != 0)
        {
            $category_id = (int)$_GET['c'];
            
            // Get list of entity types with the category $category_id
            $entity_types = ORM::factory('static_entity_type')->where('category_id', $category_id)->find_all();
            
            // Add entity types to $entity_type_ids
            foreach ($entity_types as $type)
            {
                array_push($entity_type_ids, $type->id);
            }
        }
        
        // To hold the where clauses for the query
        $where_entity_type_id = '1=1';
        if (count($entity_type_ids) > 0)
        {
            $where_entity_type_id = 'static_entity_type_id IN ('.implode(',', $entity_type_ids).')';
        }

        // Break apart location variables
        $southwest = isset($_GET['sw'])? explode(",", $_GET['sw']) : array();
        $northeast = isset($_GET['ne'])? explode(",", $_GET['ne']) : array();

        // To hold the lat/lon where clause
        $where_latlon = '1=1';
        if (count($northeast) == 2 AND count($southwest) == 2)
        {
            // Get the lat/lon values for the bounding box
			$lon_min = (float) $southwest[0];
			$lon_max = (float) $northeast[0];
			$lat_min = (float) $southwest[1];
			$lat_max = (float) $northeast[1];

            $where_latlon = array();

            // Build the where clause based on the bounding box ($ne and $sw values)
            $where_clause = arr::merge($where_latlon, array(
                'latitude >=' => $lat_min,
                'latitude <=' => $lat_max,
                'longitude >=' => $lon_min,
                'longitude <=' => $lon_max
            ));
        }
        
        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page'),
            'total_items' => ORM::factory('static_entity')
                                ->where($where_entity_type_id)
                                ->where($where_latlon)
                                ->count_all()
        ));

        // Entities for the current page
        $entities = ORM::factory('static_entity')
                                ->where($where_entity_type_id)
                                ->where($where_latlon)
                                ->find_all((int) Kohana::config('settings.items_per_page'), $pagination->sql_offset);
        
        // Extract URL variables
        $this->template->content->entities = $entities;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;

        $this->template->header->header_block = $this->themes->header_block();
    }

    /**
     * View the entity specified in @param $entity_id
     * @param  $entity_id
     */
    public function view($entity_id)
    {
        // To hold the $entity specified in @param $entity_id
        $entity = "";

        // Verify that $entity_id is numeric, else
        if (is_int($entity_id) AND $entity_id > 0)
        {
            $entity = ORM::factory('static_entity', $entity_id);
        }
        else
        {
            // Redirect to the entities list page
            url::redirect('entities/');
        }

        // TODO Set up forms array with keys corresponding to the input field names
        
        // Check if the form has been submitted, setup validation
        if ($_POST)
        {
            // Set up validation
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add validation rules, the input field, followed by some checks in that order

            // Validation passed?
        }

        // Load the view
        $this->template->content = new View('frontend/entity_view');


        $this->template->content->$entity = $entity;

        // Javascript header
//        $this->temaplate->js = new View('frontend/js/entity_view_js');
    }
}
?>