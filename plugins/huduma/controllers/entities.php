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
	
	/**
	 * Whether the current user has access to the dashboards
	 * @var boolean
	 */
	private $is_dashboard_user;
	
	/**
	 * ORM reference to the currently logged in user
	 * @var ORM
	 */
	private $current_user = "";
	
	/**
	 * No. of report items to show per view
	 * @var int
	 */
	private $report_items_per_view = 3;
	
	
	public function __construct()
	{
		parent::__construct();

		// Check if there's a dashboard user currently logged in
		$auth_lite = Authlite::instance('authlite');
		$this->is_dashboard_user = $auth_lite->logged_in();
		$this->current_user = ($this->is_dashboard_user)? $auth_lite->get_user() : NULL;
	}
	
	public function index()
	{
		// Cacheable controller
		$this->is_cachable = TRUE;

		$this->template->header->this_page = 'entities';
		$this->template->content = new View('huduma/entities');

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
							->where($where_clause)
							->count_all()
		));

		// Entities for the current page
		$entities = ORM::factory('static_entity')
					->where($where_entity_type_id)
					->where($where_latlon)
					->where($where_clause)
					->find_all((int) Kohana::config('settings.items_per_page'), $pagination->sql_offset);

		// Extract URL variables
		$this->template->content->entities = $entities;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;

		$this->template->header->header_block = $this->themes->header_block();
    }

	public function view($entity_id = FALSE, $saved = FALSE)
	{
		// Entity id not specified, redirect to entity listing
		if ( ! Static_Entity_Model::is_valid_static_entity($entity_id))
		{
			url::redirect('entities');
		}

		$this->template->content = new View("huduma/entity_view");

		// Get the entity
		$entity = ORM::factory('static_entity', $entity_id);

		$show_metadata = FALSE;

		// ucfirst() conversion each word in the string
		$entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
		
		// Get the neighbouring entities
		$neighbours = Static_Entity_Model::get_neighbour_entities($entity_id);

		$this->template->content->entity_id = $entity->id;
		$this->template->content->entity_name = $entity_name;
		$this->template->content->boundary_id = $entity->boundary_id;
		$this->template->content->latitude = $entity->latitude;
		$this->template->content->longitude = $entity->longitude;		
		$this->template->content->show_dashboard_panel = FALSE;
		
		// Set the neighbouring facilities
		$this->template->content->neighbour_facilities = $neighbours;
		
		// Set the URL to be used by the report filter
		$this->template->content->fetch_url = url::site().'entities/get_reports/?id='.$entity_id;
		
		// Setup metadata pagiantion
		$metadata_pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => 5,
			'total_items' => $entity->static_entity_metadata->count(),
			// 'style' => 'huduma',
		));
		
		$metadata = ORM::factory('static_entity_metadata')
			->where('static_entity_id', $entity_id)
			->find_all(5, $metadata_pagination->sql_offset);
			
		$metadata_view = new View('huduma/entity_metadata_view');
		$metadata_view->metadata = $metadata;
		$metadata_view->entity = $entity;
		$metadata_view->metadata_pagination = $metadata_pagination;
		
		$this->template->content->entity_metadata_view = $metadata_view;


		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => 10,
			'total_items' => ORM::factory('incident')
							->where(array('incident_active' => 1, 'static_entity_id' => $entity_id))
							->find_all()
							->count(),
			'style' => 'huduma'
		));
		
		// Show the reports
		$reports = Static_Entity_Model::get_reports($entity_id, NULL, 0, $pagination->items_per_page);
		
		$this->template->content->entity_reports_view = navigator::get_reports_view($reports, 'reports/view/', $pagination);

		//Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->js = new View('js/entity_view_js');
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
		$this->themes->js->entity_id = $entity_id;

		if ( ! $entity->longitude OR ! $entity->latitude)
		{
			$this->themes->js->latitude = Kohana::config('settings.default_lat');
			$this->themes->js->longitude = Kohana::config('settings.default_lon');
		}
		else
		{
			$this->themes->js->latitude = $entity->latitude;
			$this->themes->js->longitude = $entity->longitude;
		}

		$this->template->header->header_block = $this->themes->header_block();
	}
    
    /**
     * Performs comment rating
     */
    public function rate_report()
    {
        // No template, disable auto-rendering
        $this->template = "";
        $this->auto_render = FALSE;
        
        // Check for POST
        if ($_POST)
        {
            // Setup validation
            $validation = Validation::factory($_POST);
            
            // Add some filters
            $validation->pre_filter('trim', TRUE);
            
            // Add some rules
            $validation->add_rules('comment_id', 'required', array('Static_Entity_Comment_Model', 'is_valid_static_entity_comment'));
            $validation->add_rules('action', 'required');
            
            // Validate
            if ($validation->validate())
            {
                // Load the comment instance
                $entity_comment = new Static_Entity_Comment_Model($validation->comment_id);
                
                // Get current comment rating and date
                $comment_rating = $entity_comment->comment_rating;
                $comment_date = $entity_comment->comment_date;
                
                // Check for action
                if ($validation->action == 'add')
                {
                    // Increase the rating
                    $comment_rating++;
                }
                elseif ($validation->action == 'subtract')
                {
                    // Reduce the rating
                    $comment_rating -= ($comment_rating > 0)? 1 : 0;
                }
                
                // Set the new comment rating and save
                $entity_comment->comment_rating = $comment_rating;
                $entity_comment->comment_date = $comment_date;
                $entity_comment->save();
                
                // Return new comment value
                header("Content-type: application/json; charset=utf-8");
                
                print json_encode(array(
                    'success' => TRUE,
                    'data' => $comment_rating
                ));
            }
        }
    }
    
    /**
     * Loads the view for submitting a static entity report
     */
    public function report($entity_id)
    {
        $this->template = new View('huduma/entity_report_submit');
        
        // Setup form fields
		$form = array
		(
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'person_first' => '',
			'person_last' => '',
			'person_email' => '',
		);
		
    	// Initialize Default Values
    	$form['incident_date'] = date("m/d/Y",time());
    	$form['incident_hour'] = "12";
    	$form['incident_minute'] = "00";
    	$form['incident_ampm'] = "pm";
    	
    	// Copy form as errors
		$errors = $form;
		
		$this->template->static_entity_id = $entity_id;
        $this->template->form = $form;
        $this->template->errors = $errors;
        $this->template->form_saved = FALSE;
        $this->template->form_error = FALSE;
    }
    
	/**
	 * Handles report submission via the static entity page
	 */
	public function report_submit($entity_id)
	{
		// Set temlate to empty string and disable auto rendering
		$this->template = "";
		$this->auto_render = FALSE;

		header("Content-type: application/json; charset=utf-8");
		if ($_POST)
		{
			$location = Static_Entity_Model::get_as_location($entity_id);
            
			// Proceed if location is valid
			if ($location)
			{
				// Setup validation
				$post = Validation::factory($_POST)
							->pre_filter('trim')
							->add_rules('incident_title', 'required', 'length[3,200]')
							->add_rules('incident_description', 'required')
							->add_rules('incident_date', 'required', 'date_mmddyyyy')
							->add_rules('incident_hour', 'required', 'between[1,12]')
							->add_rules('incident_minute', 'required', 'between[0,59]');
                                
				// Merideim validation for the incident time
				if ($post->incident_ampm != "pm" AND $post->incident_ampm != "am")
				{
					$post->add_error('incident_ampm', 'values');
				}
                
				// Test validation
				if ($post->validate())
				{
					// Success! Save Location
					$location->save();

					// Create Incident_Model instance and set properties
					$incident = new Incident_Model();
					$incident->incident_title = $post->incident_title;
					$incident->incident_description = $post->incident_description;
					$incident->location_id = $location->id;
					$incident->static_entity_id = $entity_id;
					$incident->boundary_id = ORM::factory('static_entity', $entity_id)->boundary_id;

					// Set additional incident properties
					$incident_date = explode("/", $post->incident_date);

					// The $_POST['date'] is a value posted by form in mm/dd/yyyy format
					$incident_date = $incident_date[2]."-".$incident_date[0]."-".$incident_date[1];

					$incident_time = $post->incident_hour.":".$post->incident_minute.":00 ".$post->incident_ampm;

					// NOTE: The date and time stamps are MySQL specific
					$incident->incident_date = date( "Y-m-d H:i:s", strtotime($incident_date . " " . $incident_time) );
					$incident->incident_dateadd = date("Y-m-d H:i:s",time());
					$incident->save();
					
					// SAVE CATEGORY
					$incident_category = new Incident_Category_Model();
					$incident_category->incident_id = $incident->id;
					$incident_category->category_id = ORM::factory('static_entity', $entity_id)->static_entity_type->category_id;
					$incident_category->save();
                    
					// Extract personal information
					$incident_person_data = array($_POST, 'person_first', 'person_last', 'person_email');
					$incident_person_data = array_merge($incident_person_data, array(
						'person_date' => date("Y-m-d H:i:s",time()), 
						'incident_id' => $incident->id, 
						'location_id' => $location->id
					));

					$incident_person = new Incident_Person_Model();
                    
					// Validate incident person data
					// TODO: Check if valiation goes through even when personal information is not provided
					if ($incident_person->validate($incident_person_data))
					{
						// SUCCESS! Save personal information
						$incident_person->save();
					}
                    
					// Incident successfully submitted
					print json_encode(array(
						'success' => TRUE, 
						'data' => Kohana::lang('ui_main.reports_submitted')
					));
				}
				else
				{
					// FAIL
					print json_encode(array(
						'success' => FALSE, 
						'data'  => $post->as_array(),
						'error' => $post->errors('report')
					));
				}
			}
			else
			{
				// Location validation fail
				print json_encode(array('success' => FALSE));
			}
		}
		else
		{
			// Invalid REQUEST method
			print json_encode(array('success' => FALSE));
		}
	}
	
	// Returns the HTML for the metadata view
	public function get_metadata_view()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Validation
		if ( ! Static_Entity_Model::is_valid_static_entity($_GET['entity_id']))
		{
			print "";
		}
		else
		{
			// Get the entity id
			$entity  = ORM::factory('static_entity', $_GET['entity_id']);
			
			// Set up pagination
			$metadata_pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => 5,
				'total_items' => $entity->static_entity_metadata->count(),
			));
			
			// Get the metadata
			$metadata = ORM::factory('static_entity_metadata')
				->where('static_entity_id', $entity->id)
				->find_all(5, $metadata_pagination->sql_offset);
			
			// Load the metadata view and set the content
			$view = new View('huduma/entity_metadata_view');
			$view->metadata = $metadata;
			$view->entity = $entity;
			$view->metadata_pagination = $metadata_pagination;
			
			// Output
			print $view;
			
		}
		
	}
	
	/**
	 * Filters the reports for a given static entity
	 */
	public function get_reports()
	{
		// Blank out the template and disable auto rendering of the view content
		$this->template = '';
		$this->auto_render = FALSE;
		
		// Grab the URL parameters
		$entity_id = $_GET['id'];
		if (Static_Entity_Model::is_valid_static_entity($entity_id))
		{
			// Get the status
			$filter = $_GET['filter'];
			
			// Fetch the reports
			$reports = Static_Entity_Model::get_reports($entity_id, $filter);
			
			// Check if any records have been returned
			if ($reports->count() > 0)
			{
				// Report content paginator
				$pagination = $this->_get_report_paginator($entity_id, $filter);
				
				// Generate the reports view
				$reports_view = navigator::get_reports_view($reports, 'reports/view/', $pagination);
				
				print $reports_view;
			}
			else
			{
				// No results found
				print "";
			}
		}
		else
		{
			print "";
		}
	}
	
	/**
	 * Gets the paginator for entity reports
	 * 
	 * @param int $entity_id Database id of the static entity/facility
	 * @param int $status filter
	 * @return Pagination Pagination object
	 */
	private function _get_report_paginator($entity_id, $status_filter = FALSE)
	{
		// Ticket status values
		$report_filters = array('unresolved' => 1, 'resolved' => 2);
		
		if ($status_filter AND array_key_exists($status_filter, $report_filters))
		{
			return new Pagination(array(
				'style' => 'huduma',
				'query_string' => 'page',
				'items_per_page' => $this->report_items_per_view,
				'total_items' => $this->db->from('incident')
									->join('incident_category', 'incident.id', 'incident_category.incident_id')
									->join('incident_ticket', 'incident.id', 'incident_ticket.incident_id')
									->where(array(
										'incident.incident_active' => 1, 
										'incident.static_entity_id' => $entity_id,
										'incident_ticket.report_status_id' => $report_filters[$status_filter]))
									->get()
									->count()
			));
		}
		else
		{
			return new Pagination(array(
				'style' => 'huduma',
				'query_string' => 'page',
				'items_per_page' => $this->report_items_per_view,
				'total_items' => $this->db->from('incident')
									->join('incident_category', 'incident.id', 'incident_category.incident_id')
									->where(array('incident.incident_active' => 1, 'incident.static_entity_id' => $entity_id))
									->get()
									->count()
			));
		}
		
	}
}
?>
