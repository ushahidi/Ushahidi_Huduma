<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles operations for the dashboard roles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Roles
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Roles_Controller extends Admin_Controller {

	public function index()
	{
		// View for the landing page
		$this->template->content = new View('admin/dashboard/roles');

		// Status tracking variables
		$form_saved = FALSE;
		$form_error = FALSE;
		$form_action = "";

		// Check if the form has been submitted
		if ($_POST)
		{
		    // Get the specified action
		    $action = $_POST['action'];

			if ($action == 'd')
			{
			    Kohana::log('debug', 'Deleting dashboard role(s)');
			    
				// Setup validation
				$validation = Validation::factory($_POST);
				
				// Add some filters
				$validation->pre_filter('trim');
				
				// Add rules
				$validation->add_rules('dashboard_role_id.*', 'required');
				
				if ($validation->validate())
				{
				    Kohana::log('debug', 'Validation succeeded');
				    foreach ($validation->dashboard_role_id as $role_id)
				    {
				        Kohana::log('debug', 'Deleting role id: '.$role_id);
				        
				        Dashboard_Role_Model::remove_role($role_id);
				    }
				}
				else
				{
				    Kohana::log('debug', 'Validation failed! Selected records not deleted');
				}
				
			}
		}

		// Get the no. of items to be displayed per page from the config
		$items_per_page  = (int) Kohana::config('settings.items_per_page_admin');

		// Set up pagination
		$pagination  = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $items_per_page,
			'total_items' => ORM::factory('dashboard_role')->count_all()
		));

		// Get the roles to be displayed in the current page
		$dashboard_roles = ORM::factory('dashboard_role')->find_all($items_per_page, $pagination->sql_offset);

		// Set the contents of the view
		$this->template->content->form_action = $form_action;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_error = $form_error;
		$this->template->content->dashboard_roles = $dashboard_roles;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;

		// Javascript header
		$this->template->js = new View('js/dashboard_roles_js');
	
	}

	/**
	 * Saves a dashboard role
	 */
	public function save()
	{
		// Load up the view
		$this->template = "";
		$this->auto_render = FALSE;

		// To hold the output json string
		$output_json = "";

		if ($_POST)
		{
			// Manually specify the fields to be validated
			$data = arr::extract($_POST, 'id', 'name', 'description', 'agency_id', 'action', 
				'category_id', 'boundary_id', 'static_entity_id', 'can_close_issue');

			$action = $data['action'];
			
			// Instance for validation
			$role = new Dashboard_Role_Model($data['id']);

			// Validation
			if ($role->validate($data))
			{
				// Success! Save
				$role->save();

				// Build output JSON string
				$output_json = json_encode(array(
					'success' => TRUE,
					'message' => Kohana::lang('ui_huduma.dashboard_role_saved')
				));
				
			}
			else
			{
				Kohana::log('debug', Kohana::debug($data->errors()));
				// Validation failed
				$output_json = json_encode(array(
					'success' => FALSE,
					'message' => Kohana::lang('ui_main.validation_error').'<div>'.$data->errors('dashboard_privileges').'</div>'
				));
			}
		}

		// Print the JSON string
		header("Content-type: application/json; charset=utf-8");
		print $output_json;
	}
	
	/**
	 * Loads the contents for the add/edit dashboard role dialog
	 * 
	 * @param int $dashboard_role_id
	 */
	public function get($dashboard_role_id = FALSE)
	{
		// Load the view
		$this->template = new View('admin/dashboard/role_dialog');

		// Set up form fields
		$form = array(
			'id' => '',
			'name' => '',
			'description' => '',
			'agency_id' => '',
			'static_entity_id' => '',
			'boundary_id' => '',
			'category_id' => '',
			'can_close_issue' => ''
		);

		Kohana::log('info', sprintf('Validating dashboard role id %d', $dashboard_role_id));

		// To hold the category for the agency
		$agency_category = 0;

		// To hold the CSS for the category selection <div>
		$category_container_css = "";
		
		// Validate the specified role
		if (Dashboard_Role_Model::is_valid_dashboard_role($dashboard_role_id))
		{
			Kohana::log('info', 'Successfully validated role');

			// Load selected role
			$role = new Dashboard_Role_Model($dashboard_role_id);

			// Setup the form fields for the dialog
			$form = array(
				'id' => $dashboard_role_id,
				'name' => $role->name,
				'description' => $role->description,
				'agency_id' => $role->agency_id,
				'static_entity_id' => $role->static_entity_id,
				'boundary_id' => $role->boundary_id,
				'category_id' => $role->category_id,
				'can_close_issue' => $role->can_close_issue
			);

			// Get the category for which the selected agency belongs to
			if ($role->agency_id != 0)
			{
				$agency_category = ORM::factory('agency', $role->agency_id)->category_id;
				$category_container_css = "display: none;";
			}
		}

		// Fetch content for the dropdowns
		$agencies = Agency_Model::get_agencies_dropdown();
		$agencies[0] = "---".Kohana::lang('ui_huduma.select_agency')."---";
		ksort($agencies);

		$entities = Static_Entity_Model::get_entities_dropdown($agency_category);
		$entities[0] = "---".Kohana::lang('ui_huduma.select_entity')."---";
		ksort($entities);

		$categories = Category_Model::get_dropdown_categories();
		$categories[0] = "---".Kohana::lang('ui_huduma.select_category')."---";
		ksort($categories);
		
		$boundaries = Boundary_Model::get_boundaries_dropdown();
		$boundaries[0] = "---".Kohana::lang('ui_huduma.select_boundary')."---";
		ksort($boundaries);
		
		// Set the content for the view
		$this->template->form = $form;
		$this->template->agencies = $agencies;
		$this->template->categories = $categories;
		$this->template->category_container_css = $category_container_css;
		$this->template->entities = $entities;
		$this->template->boundaries = $boundaries;

	}

	public function entities()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		$json_output = "";

		if ($_POST)
		{
			// Get the agency id
			$agency_id = $_POST['agency_id'];

			$category_id = 0;

			Kohana::log('debug', sprintf('Fetched agency id: %s', $agency_id));


			// Determine the category id value to use
			if ($_POST['category_id'] != 0 AND $agency_id == 0 AND Category_Model::is_valid_category($_POST['category_id']))
			{
				Kohana::log('debug', sprintf('Using value from category dropdown. ID %s', $_POST['category_id']));

				$category_id = $_POST['category_id'];
			}
			elseif ($agency_id != 0 AND $_POST['category_id'] == 0)
			{
				$category_id = (Agency_Model::is_valid_agency($agency_id))
						? ORM::factory('agency', $agency_id)->category_id
						: 0;

				Kohana::log('debug', sprintf('Using category for the selected agency. ID %s', $category_id));
			}

			// Build output JSON
			$json_output  = json_encode(array(
				'success' => TRUE,
				'data' => Static_Entity_Model::get_entities_dropdown($category_id)
			));
		}
		else
		{
			$json_output = json_encode(array(
				'success' => FALSE
			));
		}

		// Flush the
		header("Content-type: application/json; charset=utf-8");
		print $json_output;
	}
	
}
?>
