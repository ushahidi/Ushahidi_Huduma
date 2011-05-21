<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Hook for the huduma plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma plugin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class huduma
{
	/**
	 * Registers the main event method - add
	 */
	public function __construct()
	{
		// To hold the values for the ticket associated with an incident
		$this->ticket_id = "";
		$this->agency_id = "";
		$this->report_priority_id = "";
		$this->report_status_id = "";
		$this->boundary_id = "";
		
		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		Event::add('system.pre_controller', array($this, 'add'));
	}

	/**
	 * Adds all the events to the Ushahidi main application
	 */
	public function add()
	{
		// Add CSS and Javascript
		plugin::add_stylesheet('huduma/views/css/huduma');
		plugin::add_stylesheet('huduma/views/css/facebox');
		plugin::add_javascript('huduma/views/js/facebox');
		plugin::add_javascript('huduma/views/js/jquery.horiz-bar-graph.min');
		
		// Queue events
		Event::add('ushahidi_action.nav_main_right_tabs', array($this, 'add_huduma_tab'));
		Event::add('ushahidi_action.header_scripts', array($this, 'modify_header_scripts'));
		Event::add('ushahidi_action.orm_validate_comment', array($this, 'orm_validate_comment'));

		if (Router::$controller == 'main')
		{
			// Add charting javascript
			plugin::add_javascript('../media/js/raphael');
			plugin::add_javascript('huduma/views/js/g.raphael-min');
			plugin::add_javascript('huduma/views/js/g.pie-min');
			
			// Queue up events
			Event::add('ushahidi_action.main_sidebar', array($this, 'add_main_page_sidebar'));
			Event::add('huduma_action.main_content', array($this, 'add_dashboard_snapshots'));
			
			// Modify the header scripts
			Event::add('ushahidi_action.huduma_overlay_js', array($this, 'overlay_js'));

			// Calls the Javascript used function to overlay data on the main map
			Event::add('ushahidi_action.main_map_overlay', array($this, 'overlay_main_map_js_call'));
		}
		elseif (Router::$controller == 'reports')
		{
			switch (Router::$method)
			{
				// Hook into report add/edit in admin
				case 'edit':
					// When the report is loaded
					Event::add('ushahidi_action.report_form_admin', array($this, 'report_form'));
					
					// When the report is submitted for validation and saving
					Event::add('ushahidi_action.report_submit_admin', array($this, 'report_submit_admin'));
					
					// When report validation succeeds
					Event::add('ushahidi_action.report_edit', array($this, 'report_edit_admin'));
					
					break;
				
				// Hook into report submit on the frontend
				case 'submit':
					// When the report is submitted for validation and saving
					Event::add('ushahidi_action.report_submit_frontend', array($this, 'report_submit_frontend'));
					
					// When validation succeeds and the report has been saved
					Event::add('ushahidi_action.report_add', array($this, 'report_add'));
				break;
				
				// Hook into report view on the main reports page on frontend
				case 'view':
					Event::add('ushahidi_action.report_extra', array($this, 'load_report_comments_form'));
					Event::add('ushahidi_action.report_meta', array($this, 'show_report_ticket_info'));
				break;
				
				// Hook into report view on the static entity reports page
				
			}
		}
	}

    /**
     * Adds a service delivery menu to the list of admin menus on the right of the admin console
     */
    public function add_huduma_tab()
    {
        // Get the current event data
        $main_right_tabs = Event::$data;

        // Generate the menu string
        $main_right_tabs = arr::merge($main_right_tabs, array(
            'servicedelivery' => Kohana::lang('ui_huduma.huduma'))
        );
        
        // Set the event data
        Event::$data = $main_right_tabs;
    }


	/**
	 * Renders the javascript for d
	 */
	public function modify_header_scripts()
	{
		// $no_conflict = View::factory('js/no_conflict_js');
		// $no_conflict->render(TRUE);
		
		$toggle_button_js = new View('js/toggle_login_button_js');
		$toggle_button_js->render(TRUE);
	}
	
    /**
     * Renders the javascript to render the static entities overlay
     */
    public function overlay_js()
    {
		// Load the JavaScript header for rendering the overlay data
		$overlay_js = new View('js/overlays_js');

		// TODO Check the plugin settings to determine the json URL
		$overlay_js->overlay_json_url = 'overlays/cluster';
		$overlay_js->overlay_layer_name = 'Administrative Units';

		// Append the overlay js to the current data
		$overlay_js->render(TRUE);

    }

	/**
	 * Echoes the Javascript function for overlaying data on the main map. This JS function
	 * should already have been defined in the javascript header file loaded in this->modify_header_scripts()
	 * above
	 */
	public function overlay_main_map_js_call()
	{
		// Call the JS function to render the overlay data
		echo 'overlayMainMap();';
	}
    
	/**
	 * Callback function for the "ushahidi.orm_validate_comment" event
	 *
	 * Performs validation on the extra columns added to the comment table by this (Huduma) plugin
	 */
	public function orm_validate_comment()
	{
		// Get the event data for modification
		$array = Event::$data;

		// Check if a static entity id has been specified
		if ( ! empty($array->static_entity_id) AND $array->static_entity_id != 0)
		{
			$array->add_rules('static_entity_id', array('Static_Entity_Model', 'is_valid_static_entity'));
		}

		// Check if the dashboard user id is in the validation data
		if ( ! empty($array->dashboard_user_id) AND $array->dashboard_user_id != 0)
		{
			// Ensure the dashboard user id is validated
			$array->add_rules('dashboard_user_id', array('Dashboard_User_Model', 'is_valid_dashboard_user'));
		}
	}
    
	/**
	 * Callback for the ushahidi_action.report_form_admin event
	 *
	 * Loads the form for assigning an incident repor to a service agency
	 */
	public function report_form()
	{
		// Load the view
		$report_form = View::factory('admin/report_action_form');
		
		// Set up form fields for the action view
		$form = array(
			'ticket_id' => '',
			'report_agency_id' => '',
			'report_status_id' => '',
			'report_priority_id' => ''
		);
		
		// Get the ID of the incident report
		$id = Event::$data;
		
		// Load the incident and get the categories under it
		$incident = new Incident_Model($id);
		
		// To hold the incident catgories
		$categories = array();
		foreach ($incident->incident_category as $category)
		{
			array_push($categories, $category->category_id);
		}

		// Get the boundary for the incident
		$boundaries = array(0);
		if ( ! empty($incident->boundary_id) AND $incident->boundary_id != 0)
		{
			array_push($boundaries, $incident->boundary_id);
		}
		
		// Check for static entity report
		if ( ! empty($incient->static_entity_id) AND $incident->static_entity_id != 0)
		{
			$entity = ORM::factory('static_entity', $incident->static_entity_id);
			if ( ! empty($entity->boundary_id) AND $entity->boundary_id != 0)
			{
				// * NOTES
				// Static entity reports are already assigned to the person in charge of the entity
				// Assignment to an agency is more supervisory
				array_push($boundaries, $entity->boundary_id);
			}
		}
		
		// Check if a ticket exists for the incident
		$ticket = Incident_Ticket_Model::get_incident_ticket($id);
		if ($ticket)
		{
			// Set the form fields
			$form['ticket_id']  = $ticket->id;
			$form['report_agency_id'] = $ticket->agency_id;
			$form['report_priority_id'] = $ticket->report_priority_id;
			$form['report_status_id'] = $ticket->report_status_id;
		}
		
		// Get the list of agencies within the incidents category
		$agencies = ORM::factory('agency')
						->in('category_id', $categories)
						->in('boundary_id', $boundaries)
						->select_list('id', 'agency_name');

		$agencies[0] = "---".Kohana::lang('ui_huduma.select_agency')."---";
		ksort($agencies);
		
		// Set content for the view
		$report_form->form = $form;
		$report_form->agencies = $agencies;
		$report_form->report_status = ORM::factory('report_status')->select_list('id', 'status_name');
		$report_form->report_priority = ORM::factory('report_priority')->select_list('id', 'priority_name');
		$report_form->incident_id = $id;
		
		// Display
		$report_form->render(TRUE);
	}
	
	/**
	 * Event callback to be executed when the report edit form in the admin 
	 *  is submitted (via POST)
	 */
	public function report_submit_admin()
	{
		// Get the validation object in the event data
		$post = Event::$data;
		if ( ! empty($post->ticket_id))
		{
			// Add validation rule
			$post->add_rules('ticket_id', array('Incident_Ticket_Model', 'is_valid_incident_ticket'));
			
			// Save value to memory
			$this->ticket_id = $post->ticket_id;
		}
		
		// Agency id validation
		if ( ! empty($post->report_agency_id) AND $post->report_agency_id != 0)
		{
			// Save the current agency id
			$this->agency_id = $post->report_agency_id;
			
			// Add validation rule for the agency
			$post->add_rules('report_agency_id', array('Agency_Model', 'is_valid_agency'));
		}
		
		// Validation rules for report status and priotiy
		$post->add_rules('report_status_id', array('Report_Status_Model', 'is_valid_report_status'));
		$post->add_rules('report_priority_id', array('Report_Priority_Model', 'is_valid_report_priority'));
		
		// Save the status and priority id's to memory - for referencing when validation succeeds
		$this->report_status_id = $post->report_status_id;
		$this->report_priority_id = $post->report_priority_id;
	}
	
	/**
	 * Event callback to be executed when the validation of the report edit form in the admin
	 * succeeds. Validation takes place after the form is posted.
	 */
	public function report_edit_admin()
	{
		// Get the incident
		$incident = Event::$data;
		
		// Data values to check
		$data = array(
			'incident_id' => $incident->id,
			'static_entity_id' => $incident->static_entity_id,
			'agency_id' => $this->agency_id,
			'report_status_id' => $this->report_status_id,
			'report_priority_id' => $this->report_priority_id
		);
		
		// Incident_Ticket_Model instance for validation and subsequent saving to the DB
		$incident_ticket = ( ! empty($this->ticket_id))
							? new Incident_Ticket_Model($this->ticket_id) 
							: new Incident_Ticket_Model();
		
		// Validate
		if ($incident_ticket->validate($data))
		{
			// SUCCESS! Save
			$incident_ticket->save();
		}		
	}
	
	/**
	 * Event callback method to be executed when a report is submitted for validaton
	 * from the frontend
	 */
	public function report_submit_frontend()
	{
		// Get the validation object
		$post = Event::$data;
		
		// Ensure the either a constituency or a county is specified
		if (empty($post->county_id) AND empty($post->constituency_id))
		{
			$post->add_error('constituency_county', 'boundaries');
		}
		
		// Add extra validation rules as necessary
		if ( ! empty($post->county_id) AND $post->county_id != 0)
		{
			$post->add_rules('county_id', array('Boundary_Model', 'is_valid_boundary'));
			$this->boundary_id = $post->county_id;
		}
		
		if ( ! empty($post->constituency_id) AND $post->constituency_id != 0)
		{
			$post->add_rules('constituency_id', array('Boundary_Model', 'is_valid_boundary'));
			$this->boundary_id = $post->constituency_id;
		}
	}
	
	/**
	 * Event callback to be executed when a report is saved via the frontend
	 */
	public function report_add()
	{
		// Get the incident
		$incident = Event::$data;
		
		// Set the boundary id
		$incident->boundary_id = $this->boundary_id;
		$incident->save();
		
		// Clear the property
		$this->boundary_id = "";
	}
	
	/**
	 * Event callback to be executed when the main controller is accessed
	 *
	 * Loads the view containing the dashboard stats summary -  The view
	 * is rendered in the middle column of the main page
	 */
	public function add_dashboard_snapshots()
	{
		$view = View::factory('frontend/dashboard_snapshots');
		$view->category_snapshots = navigator::get_category_stats();
		$view->render(TRUE);
	}
	
	/**
	 * Event callback to be executed when the main controller is accessed
	 * Loads the view for the left sidebar of the main page
	 */
	public function add_main_page_sidebar()
	{
		// Statistics
		$stats = array(
			'total_reports' => 0,
			'resolved' => 0,
			'unresolved' => 0,
			'unassigned' => 0,
		);
		
		// Get the category stats
		$categories = navigator::get_category_stats();
		
		// Update the statistics
		foreach ($categories as $category)
		{
			$stats['total_reports'] += $category->total_reports;
			$stats['resolved'] += $category->resolved;
			$stats['unresolved'] += $category->unresolved;
		}
		
		$stats = array_merge($stats, array(
			'unassigned' => $stats['total_reports'] - ($stats['unresolved'] + $stats['resolved']))
		);
		
		$chart_colors = array_reverse(array("'#640235'", "'#CA669A'", "'#CC0033'"));
		$stats_data = array(
			$stats['resolved'], 
			$stats['unresolved'], 
			$stats['unassigned']
		);
		
		// Generate the Raphael JS
		$raphael_js = View::factory('js/main_sidebar_js');
		$raphael_js->chart_data = implode(",", $stats_data);
		$raphael_js->chart_colors = implode(",", $chart_colors);
		
		
		// Compute the percentages
		if ($stats['resolved'] > 0)
		{
			$stats['resolved'] = round(($stats['resolved']/$stats['total_reports']) * 100, 2);
		}
		
		if ($stats['unresolved'] > 0)
		{
			$stats['unresolved'] = round(($stats['unresolved']/$stats['total_reports']) * 100, 2);
		}
		
		if ($stats['unassigned'] > 0)
		{
			$stats['unassigned'] = round(($stats['unassigned']/$stats['total_reports']) * 100, 2);
		}
		
		// TODO: Compute issue resolution rate
		
		// Load the view
		$view = View::factory('frontend/main_page_sidebar');
		$view->stats = $stats;
		$view->main_sidebar_js = $raphael_js;
		$view->render(TRUE);
	}
	
	/**
	 * Event callback for the ushahidi_action.report_extra event
	 *
	 * Loads a custom comments form
	 */
	public function load_report_comments_form()
	{
		// Get the incident id
		$incident_id = Event::$data;
		
		// Setup forms
		$form = array(
			'comment_description' => '', 
			'comment_author' => '', 
			'comment_email' => ''
		);
		
		// Check if a dashboard user is logged in
		$auth_lite = Authlite::instance('authlite');
		$is_dashboard_user = $auth_lite->logged_in();
		$can_close_issue = FALSE;
		
		// If user is logged in, check if they can close an issue
		if ($is_dashboard_user)
		{
			$role = $auth_lite->get_user()->dashboard_role;
			$can_close_issue = (bool) $role->can_close_issue;
		}
								
		// Load and display the view
		$comments_form = View::factory('frontend/report_comments_form');
		$comments_form->incident_id = $incident_id;
		$comments_form->form = $form;
		$comments_form->form_error = FALSE;
		$comments_form->form_saved = FALSE;
		$comments_form->captcha = Captcha::factory();
		$comments_form->is_dashboard_user = $is_dashboard_user;
		$comments_form->can_close_issue = $can_close_issue;
		$comments_form->render(TRUE);
	}
	
	public function show_report_ticket_info()
	{
		// Get the incident id
		$incident_id = Event::$data;
		$ticket = Incident_Ticket_Model::get_incident_ticket($incident_id);
		
		// Build the HTML
		if ($ticket)
		{
			$incident = ORM::factory('incident', $incident_id);
			$meta_html = '<div class="report-ticket-info"><span class="state state-';
			$meta_html .= ($ticket->report_status_id==1)? 'open' : 'closed';
			$meta_html .= '">'
						. $ticket->report_status->status_name
						. '</span>'
						. '<p><strong>'.$incident->comment->count().'</strong>'.strtolower(Kohana::lang('ui_main.comments')).'</p>'
						. '</div>';

			Kohana::log('debug', $meta_html);
			print $meta_html;
		}
	}
	
}

// Instantiate the hook
new huduma();