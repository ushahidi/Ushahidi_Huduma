<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Dashboard home page controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Home page
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Home_Controller extends Dashboard_Template_Controller {

    /**
     * Dashboard landing page
     */
	public function index()
	{
		// Setup the form
		$form = array(
			'incident_id' => '',
			'comment_description' => ''
		);

		// Copy the form as errors, so the errors maintain the same keys as the field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		if ($this->static_entity_role)
		{
			// Load the static entity view
			$this->template->content = new View('frontend/entity_view');
			
			// Load the static entity
			$entity = ORM::factory('static_entity', $this->static_entity_id);
			
			// ucfirst() type conversion on the entity name
			$entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
			
			// Set the entity name
			$this->template->content->entity_name = $entity_name;
			$this->template->content->entity = $entity;
			
			// Disable "view metadata" link
			$this->template->content->show_metadata = FALSE;
			$this->template->content->show_dashboard_panel = TRUE;
			$this->template->content->dashboard_panel = $this->__get_dashboard_panel();
			
			// Metadata viewer
			$metadata_pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => 5,
				'total_items' => $entity->static_entity_metadata->count(),
				// 'style' => 'huduma',
			));

			$metadata = ORM::factory('static_entity_metadata')
				->where('static_entity_id', $this->static_entity_id)
				->find_all(5, $metadata_pagination->sql_offset);


			$metadata_view = new View('frontend/entity_metadata_view');
			$metadata_view->metadata = $metadata;
			$metadata_view->entity = $entity;
			$metadata_view->metadata_pagination = $metadata_pagination;

			$this->template->content->entity_metadata_view = $metadata_view;
			
			
			// Setup pagination for the entity's reports
			$pagination = new Pagination(array(
				'query_string' => 'page',
				'items_per_page' => 10,
				'total_items' => ORM::factory('incident')
								->where(array('incident_active' => 1, 'static_entity_id' => $this->static_entity_id))
								->find_all()
								->count(),
				'style' => 'huduma'
			));
			
			// Get the reports for the entity
			$reports = Static_Entity_Model::get_reports($this->static_entity_id);
			$this->template->content->entity_reports_view = navigator::get_reports_view($reports, 'dashboards/home/reports/', $pagination);
			
			$this->template->content->entity_id = $this->static_entity_id;
			
			// Javascript header
			$this->themes->map_enabled = TRUE;
			$this->themes->js = new View('js/entity_view_js');
			$this->themes->js->default_map = Kohana::config('settings.default_map');
			$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
			$this->themes->js->entity_id = $entity->id;
			$this->themes->js->entity_name = $entity_name;
			$this->themes->js->latitude = $entity->latitude;
			$this->themes->js->longitude = $entity->longitude;
			
			// Set the header block
			$this->template->header->header_block = $this->themes->header_block();
		}
		elseif ($this->agency_role)
		{
			// Role for specific agency

			// Chack if agency role is location bound, by administrative boundary

		}
		elseif ($this->category_role)
		{
			// Role for specific category

			// Check if categoruy role is location bound
		}
		elseif ($this->boundary_role)
		{
			// Role for specific admin boundary
			$this->template->content = new View('frontend/dashboards/boundary_dashboard');
			
			// Load the boundary from the database
			$boundary = ORM::factory('boundary', $this->boundary_id);
			$boundary_name = $boundary->boundary_name.' '.$boundary->get_boundary_type_name();
			
			// Get the reports for the boundary
			$reports  = Boundary_Model::get_boundary_reports($this->boundary_id);
			
			// Compute stats
			$total_resolved = 0;
			$total_unresolved = 0;
			$total_reports = $reports->count();
			foreach ($reports as $report)
			{
				$total_resolved += ($report->report_status == 2)? 1 : 0;
				$total_unresolved += ($report->report_status == 1 OR empty($report->report_status))? 1 : 0;
			}
			
			$total_resolved = ($total_resolved > 0)? round(($total_resolved/$total_reports),2) * 100 : 0;
			$total_unresolved = ($total_unresolved > 0)? round(($total_unresolved/$total_reports),2) * 100 : 0;
			
			$this->template->content->dashboard_panel = $this->__get_dashboard_panel();
			$this->template->content->boundary_name = $boundary_name;
			$this->template->content->categories = !empty($this->boundary_role)
													? Category_Model::get_dropdown_categories()
													: NULL;
			$this->template->content->total_reports = $total_reports;
			$this->template->content->total_resolved = $total_resolved;
			$this->template->content->total_unresolved = $total_unresolved;
			$this->template->content->boundary_reports_view = navigator::get_reports_view($reports, 'dashboards/home/reports/');
			
			$marker_radius = Kohana::config('map.marker_radius');
			$marker_opacity = Kohana::config('map.marker_opacity');
			$marker_stroke_width = Kohana::config('map.marker_stroke_width');
			$marker_stroke_opacity = Kohana::config('map.marker_stroke_opacity');
			
			// Javascript
			$this->themes->map_enabled = TRUE;
			$this->themes->js = new View('js/boundary_dashboard_js');
			$this->themes->js->default_map = Kohana::config('settings.default_map');
			$this->themes->js->default_zoom = 2 + (int)Kohana::config('settings.default_zoom');
			$this->themes->js->layer_file_url = !empty($boundary->boundary_layer_file)
						? url::base().Kohana::config('upload.relative_directory').'/'.$boundary->boundary_layer_file
						: "";
			$this->themes->js->layer_color = $boundary->boundary_color;
			$this->themes->js->layer_name = $boundary_name;
			$this->themes->js->boundary_id = $this->boundary_id;
			
	        $this->themes->js->marker_radius =
	            ($marker_radius >=1 && $marker_radius <= 10 ) ? $marker_radius : 5;
	        $this->themes->js->marker_opacity =
	            ($marker_opacity >=1 && $marker_opacity <= 10 )
	            ? $marker_opacity * 0.1  : 0.9;
	        $this->themes->js->marker_stroke_width =
	            ($marker_stroke_width >=1 && $marker_stroke_width <= 5 ) ? $marker_stroke_width : 2;
	        $this->themes->js->marker_stroke_opacity =
	            ($marker_stroke_opacity >=1 && $marker_stroke_opacity <= 10 )
	            ? $marker_stroke_opacity * 0.1  : 0.9;
			
			// Header block
			$this->template->header->header_block = $this->themes->header_block();
		}
		else
		{
			Kohana::log('error', 'No role found for this user');
			
			url::redirect('dashboards/logout');
		}
	}
	
	/**
	 * Shows the entity profile page for a static entity
	 */
	public function entity_profile()
	{
		// Ensure the user has a static entity role
		if ( ! $this->static_entity_role)
		{
			// Go back to home page
			$this->index();
		}

		// Load the entity view page with edit options
		$this->template->content = new View('frontend/dashboards/entity_profile');

		// Setup forms
		$form = array(
			'entity_id' => '',
			'entity_name' => '',
			'latitude' => '',
			'longitude' => '',
			'static_entity_type_id' => '',
			'agency_id' => '',
			'boundary_id' => ''
		);
	    
		// Copy form as errors
		$errors = $form;

		$form_error = FALSE;
		$form_saved = FALSE;

		// Dashboard panel
		$dashboard_panel = new View('frontend/dashboards/dashboard_panel');
		$dashboard_panel->static_entity_panel = ! empty($this->static_entity_id);
		$dashboard_panel->boundary_panel = ! empty($this->boundary_id);

		$this->template->content->dashboard_panel = $dashboard_panel;

		// Retrieve the entity
		$entity = new Static_Entity_Model($this->static_entity_id);
	    
		// Has the form been submitted - For metadata update or otherwise
		if ($_POST)
		{
			$data = arr::extract($_POST, 'entity_name', 'longitude', 'latitude', 'agency_id', 'boundary_id', 'static_entity_type_id');
	        
			// Validation
			if ($entity->validate($data))
			{
				// Success! Save
				$entity->save();

				// Turn on form_saved
				$form_saved = TRUE;
				$form_error = FALSE;

				// Set the form values
				$form = array(
					'entity_name' => $entity->entity_name,
					'latitude' => $entity->latitude,
					'longitude' => $entity->longitude,
					'static_entity_type_id' => $entity->static_entity_type_id,
					'agency_id' => $entity->agency_id,
					'boundary_id' => $entity->boundary_id,
				);
			}
			else
			{
				// Turn on the form error
				$form_error = TRUE;
				$form_saved = FALSE;

				// Populate forms and erros with new values
				$form = arr::overwrite($form, $data->as_array());
				$errors = arr::overwrite($errors, $data->errors());
			}
		}
		else
		{
			// Set the form values
			$form = array(
				'entity_name' => $entity->entity_name,
				'latitude' => $entity->latitude,
				'longitude' => $entity->longitude,
				'static_entity_type_id' => $entity->static_entity_type_id,
				'agency_id' => $entity->agency_id,
				'boundary_id' => $entity->boundary_id,
			);
		}
	    
		$entity_metadata = new View('frontend/dashboards/entity_metadata_view');
		$entity_metadata->static_entity_id = $this->static_entity_id;
		$entity_metadata->metadata_items = Static_Entity_Model::get_metadata($this->static_entity_id);

		// Set data for the content view
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_error = $form_error;
		$this->template->content->entity_types_dropdown = Static_Entity_Type_Model::get_entity_types_dropdown();
		$this->template->content->agencies_dropdown = Agency_Model::get_agencies_dropdown();
		$this->template->content->boundaries_dropdown = Boundary_Model::get_boundaries_dropdown();
		$this->template->content->entity_metadata = $entity_metadata;
        
		//  Javascript header
		$this->themes->map_enabled = TRUE;
		$this->themes->js = new View('js/entity_edit_js');
		$this->themes->js->longitude = $entity->longitude;
		$this->themes->js->latitude = $entity->latitude;
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->new_metadata_item_url = url::site().'dashboards/home/metadata_new';
		$this->themes->js->add_metadata_dialog_url = url::site().'dashboards/home/metadata_add/'.$this->static_entity_id;
		$this->themes->js->metadata_update_url = url::site().'dashboards/home/metadata_update';
		$this->themes->js .= new View('js/dashboard_common_js');
        
		// Set the header block
		$this->template->header->header_block = $this->themes->header_block();
	}
	
	/**
	 * Change password page
	 */
	public function change_password()
	{
		// Load the content view
		$this->template->content = new View('frontend/dashboards/change_password');

		// Set up form fields
		$form = array(
			'name' => $this->user->name,
			'username' => $this->user->username,
			'email' => '',
			'password' => '',
		);
	    
		// Copy forms as errors so that the erros maintain the keys corresponding to the field names
		$errors = $form;

		$form_saved = FALSE;
		$form_error = FALSE;
	    
		// Has te form been submitted
		if ($_POST)
		{
			// Manually extract the data
			$data = arr::extract($_POST, 'email', 'password', 'confirm_password', 'name', 'email', 'username');

			// Add the other properties so that the validate method doesn't throw an error
			$data = array_merge($data, array('is_active' => $this->user->is_active));
	        
			if ($this->user->validate($data) AND $_POST['save'] == 1)
			{
				// Success
				$this->user->save();

				$form_saved = TRUE;
				$form_error = FALSE;

				array_fill_keys($form, '');
			}
			else
			{
				$form = arr::overwrite($form, $data->as_array());
				$error = arr::overwrite($form, $data->errors());

				// Turn on form error
				$form_error = TRUE;

				$form_saved = FALSE;
			}
		}
		else
		{
			// Set the email key in the $forms array
			$form['email'] = $this->user->email;
		}
	    
		$dashboard_panel = new View('frontend/dashboards/dashboard_panel');
		$dashboard_panel->static_entity_panel = ! empty($this->static_entity_id);
		$dashboard_panel->boundary_panel = ! empty($this->boundary_id);

		// Set content data
		$this->template->content->dashboard_panel = $dashboard_panel;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
	    
		// Javscript header
		$this->themes->js = new View('js/dashboard_common_js');

		// Set the header block
		$this->template->header->header_block = $this->themes->header_block();
	}
	
	/**
	 * Comment moderation page
	 */
	 public function moderate_comments()
	 {
		// Load template
		$this->template->content = new View('frontend/dashboards/moderate_comments');

		// Get the no. of items per page
		$items_per_page = (int)Kohana::config('settings.items_per_page_admin');
	     
		// Setup pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $items_per_page,
			'total_items' => ORM::factory('comment')->where('static_entity_id', $this->static_entity_id)->count_all()
		));
	     
		// Fetch comments for current page
	     $comments = ORM::factory('comment')
					->where('static_entity_id', $this->static_entity_id)
					->find_all($items_per_page, $pagination->sql_offset);
	     
		$this->template->content->dashboard_panel = $this->__get_dashboard_panel();
		$this->template->content->comments = $comments;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->themes->js = new View('js/dashboard_common_js');
		$this->template->header->header_block = $this->themes->header_block();
	}
	 
	/**
	 * Updates a static entity comment
	 */
	public function update_comment()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		 // Return value
		header("Content-type: application/json; charset=utf-8");

		// Check for form POST
		if ($_POST)
		{
			// Get the comment id
			$comment_id = $_POST['comment_id'];

			// Validate comment
			if (Comment_Model::is_valid_comment($comment_id))
			{
				// Get the action
				$action = $_POST['action'];

				// Load the entity comment and fetch the comment date
				$comment = ORM::factory('comment', $comment_id);
				$comment_date = $comment->comment_date;

				// Check for the action
				switch ($action)
				{
					case 'spam':
						// Mark comment as spam
						$comment->comment_spam = 1;
					break;
					
					case 'notspam':
						// Mark comment as spam
						$comment->comment_spam = 0;
					break;
					
					case 'delete':
						// Mark comment as inactive
						$comment->comment_active = 0;
					break;
					
					case 'undelete':
						// Mark comment as active
						$comment->comment_active  = 1;
					break;
				}
				// Maintain the original comment date
				$comment->comment_date = $comment_date;

				// Save comment
				$comment->save();

				// Success!
				print json_encode(array('success' => TRUE));
			}
			else
			{
				print json_encode(array('success' => FALSE));
			}
		}
		else
		{
			print json_encode(array('success' => FALSE));
		}
	}
	 
	/**
	 * Handles metadata update
	 */
	public function metadata_update()
	{
		// No template for this method
		$this->template = "";
		$this->auto_render = FALSE;

		// Set the headers
		header("Content-type: application/json; charset=utf-8");
	     
		if ($_POST)
		{
	         if ($_POST['action'] == 's')
	         {
				// Save Action
				$data = arr::extract($_POST, 'item_value', 'item_label', 'as_of_year');
				$data = array_merge($data, array('static_entity_id' => $this->static_entity_id));

				$static_entity_metadata = new Static_Entity_Metadata_Model($_POST['id']);

				if ($static_entity_metadata->validate($data))
				{
					$static_entity_metadata->save();
					print json_encode(array('success' => TRUE));
				}
				else
				{
					print json_encode(array('success' => FALSE));
				}
			}
			elseif ($_POST['action'] == 'n')	// Adding new item(s)
			{
				navigator::save_new_metadata_items();
			}
			elseif ($_POST['action'] == 'd')
			{
				// Delete static entity metadata item
				$static_entity_metadata = ORM::factory('static_entity_metadata', $_POST['id']);
				if ($static_entity_metadata->loaded)
				{
					$static_entity_metadata->delete();

					print json_encode(array('success' => TRUE));
				}
				else
				{
					print json_encode(array('success' => FALSE));
				}
			}
		}
		else
		{
			print json_encode(array('success' => FALSE));
		}
	}
    
	/**
	 * Loads the view for the metadata dialog
	 */
	public function metadata_add($entity_id)
	{
		$this->template = new View("admin/entity_metadata_dialog");
		$this->template->static_entity_id = $this->static_entity_id;
	}

	/**
	 * Prints the HTML for adding a metadata item on the UI
	 */
	public function metadata_new()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Verify that the entity id exists
		print navigator::get_metadata_item_row();
	}
	
	/**
	 * Loads the view page for the report
	 */
	public function reports($incident_id = FALSE)
	{
		// Validate incident id
		if (Incident_Model::is_valid_incident($incident_id))
		{
			$this->template->content = new View('frontend/single_report_view');
			// Setup forms
			$form = array(
				'comment_author' => '',
				'comment_email' => '',
				'comment_description' => '',
			);
			$errors = $form;
			$form_error = FALSE;
			$form_saved = FALSE;
			
			// ORM instances
			$ticket = Incident_Ticket_Model::get_incident_ticket($incident_id);
			$incident = new Incident_Model($incident_id);
			
			// Has the form been submitted?
			if ($_POST)
			{
				// Data to be validated and saved
				$data = array(
					'incident_id' => $incident_id,
					'comment_author' => $this->user->name, 
					'comment_email'=>$this->user->email, 
					'static_entity_id'=>$incident->static_entity_id,
					'dashboard_user_id' => $this->user->id,
					'comment_date' => date('Y-m-d H:i:s')
				);
				
				// Extract the comment text
				$data = array_merge($data, arr::extract($_POST, 'comment_description'));
				
				$comment = new Comment_Model();
				if ($comment->validate($data))
				{
					// Success! Save the comment
					$comment->comment_date = date('Y-m-d H:i:s');
					$comment->save();
					
					// Is the ticket to be closed?
					$close_ticket = ($_POST['comment_close']) ? TRUE : FALSE;
					
					// Update ticket history
					$history_data = array(
						'incident_ticket_id' => $ticket->id, 
						'report_status_id' => ($close_ticket)? 2 : $ticket->report_status_id,
						'notes' => $comment->comment_description,
						'dashboard_user_id' => $this->user->id
					);
					
					$history_model = new Incident_Ticket_History_Model();
					if ($history_model->validate($history_data))
					{
						// Success! Save
						$history_model->save();
						
						// Check if the ticket is to be closed
						if ($close_ticket)
						{
							Kohana::log('info', sprintf('closing ticket #%s', $ticket->id));
							// Close the ticket
							$ticket->close();
						}
						
						$form_saved = TRUE;
						array_fill_keys($form, '');
					}
					else
					{
						Kohana::log('error', sprintf('Could not update the history for ticket #%s', $ticket->id));
						Kohana::long('debug', Kohana::debug($history_data->errors()));
					}
				}
				// Validation FAILED
				else
				{
					$form_error = FALSE;
					$form = arr::overwrite($form, $data->as_array());
					$errors = arr::overwrite($errors, $data->errors('comment'));
				}
			}
		
			// Load the comments form
			$comments_form = new View('frontend/report_comments_form');
			$comments_form->incident_id = $incident_id;
			$comments_form->is_dashboard_user =TRUE;
			$comments_form->can_close_issue = $this->can_close_issue;
			
			// Set the form content
			$comments_form->captcha = Captcha::factory();
			$comments_form->form = $form;
			$comments_form->errors = $errors;
			$comments_form->form_error = $form_error;
			$comments_form->form_saved = $form_saved;
			
			$this->template->content->show_dashboard_panel = TRUE;
			$this->template->content->dashboard_panel = $this->__get_dashboard_panel();
			$this->template->content->form = $form;
			$this->template->content->comments_form = $comments_form;
			$this->template->content->incident = $incident;
			$this->template->content->ticket = $ticket;
			
			$this->template->header->header_block = $this->themes->header_block();
		}
		else
		{
			// Invalid parameter value, redirect to dashboard home page
			$this->index();
		}
	}
	
	/**
	 * Helper method for generating the dashboard panel
	 */
	private function __get_dashboard_panel()
	{
		// Load the dashboard panel view
		$dashboard_panel = View::factory('frontend/dashboards/dashboard_panel');
		$dashboard_panel->static_entity_panel = ! empty($this->static_entity_id);
		$dashboard_panel->boundary_panel = ! empty($this->boundary_id);
		if ( ! empty($this->boundary_id))
		{
			$dashboard_panel->boundary_type_name = ORM::factory('boundary', $this->boundary_id)
													->get_boundary_type_name();
		}
		$dashboard_panel->category_panel = ! empty($this->category_id);
		$dashboard_panel->agency_panel = ! empty($this->agency_id);
		
		return $dashboard_panel;
	}
	
	/**
	 * Prints a GeoJSON string for the entities in a speicific boundary
	 */
	public function boundary_entities()
	{
		$this->template = new View('json');
		$json = "";
		
		// Fetch the zoom level entities
		if (isset($_GET['boundary_id']) AND Boundary_Model::is_valid_boundary($_GET['boundary_id']))
		{
			// Fetch the boundary id
			$boundary_id = $_GET['boundary_id'];
			
			// Fetch the zoom level
			$zoom = (isset($_GET['zoom']) AND (int)$_GET['zoom'] > 0)? (int)$_GET['zoom'] : 10;
			
			Kohana::log('info', sprintf('Fetching incidents for boundary %d at zoom level %s', $boundary_id, $zoom));
			
			// Pass URL parmaeters to the clustering helper
			$json = cluster::get_clustered_entities($boundary_id, $zoom);
		}
		
		// Print out the GeoJSON string
		header("Content-type: application/json; charset=utf-8");
		$this->template->json = $json;
	}
	
	/**
	 * Prints a HTML string for displaying reports for a specific category
	 * 
	 * @param int $category_id Category of reports to fetch
	 */
	public function category_reports($category_id)
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		if ( ! Category_Model::is_valid_category($category_id) OR !$this->boundary_role)
		{
			// Fail
			print "";
		}
		else
		{
			// Success
			
			// Get the reports for the specified category in the current boundary
			$report_html = View::factory('frontend/dashboard_report_items');
			$reports = Boundary_Model::get_boundary_reports($this->boundary_id, 'all', $category_id);
			$report_html->reports = $reports;
			$report_html->report_view_controller = 'dashboards/home/reports/';
			
			// Print the reports
			print $report_html;
		}
		
	}
	
	/**
	 * Displays a page with detailed stats about the boundary
	 *
	 * Stats include area, population, no. of facilities and their breakdown, 
	 * rate of response to queries, performance of service agencies, no. of agencies
	 * in the area and the categories under which they fall etc
	 */
	public function boundary_profile()
	{
		// TODO Implement the view pages for this
	}
	
	/**
	 * Breakdown of reports by location
	 */
	public function report_locations()
	{
		// TODO View pages and logic for this
		
		// Will require reverse Geo-coding, clustering of reports using a user defined radius
	}
}
?>
