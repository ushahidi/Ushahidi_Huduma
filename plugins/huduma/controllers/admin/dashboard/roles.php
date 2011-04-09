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

			// Check for add/update
			if ($action == 'a')
			{
				// TODO add the id of the currently selected role
				array_push($data, array('id' => $_POST['id']));
				
				// Instance to be used for validation
				$dashboard_role = new Dashboard_Role_Model();

			}
			elseif ($action == 'd')
			{
				// Get the selected role
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
			$data = arr::extract($_POST, 'id', 'name', 'description', 'agency_id', 'action');

			$action = $data['action'];
			
			// Instance for validation
			$role = new Dashboard_Role_Model($data['id']);

			// Validation
			if ($role->validate($data))
			{
				// Success! Save
				$role->save();

				// Extract privilege fields from the $_POST data
				$privileges = arr::extract($_POST, 'category_id', 'boundary_id', 'static_entity_id');

				// Add the id of the dashboard role
				$privileges = array_merge($privileges, array('dashboard_role_id' => $role->id));

				// Get the dashboard privilege entries for the current role
				
				$role_privileges_array = Dashboard_Role_Model::get_privileges($role->id);

				// To keep track of validation of privileges
				$privilege_validate = FALSE;
				
				if (count($role_privileges_array) > 0)
				{
					// Debug
					Kohana::log('info', sprintf('Updating %s privileges for role %s', count($role_privileges_array), $role->id));

					foreach ($role_privileges_array as $role_privilege)
					{
						Kohana::log('info', 'Fetching item from result set');

						// Validate selected privileges
						if ($role_privilege->validate($privileges))
						{
							// Success! Save
							$this->db->update('dashboard_role_privileges', 
									array(
										'category_id' => $role_privilege->category_id,
										'static_entity_id' => $role_privilege->static_entity_id,
										'boundary_id' => $role_privilege->boundary_id
									),
									array('dashboard_role_id' => $role->id));

							Kohana::log('info', 'Afer saving:'.Kohana::debug($role_privilege->static_entity_id));

							$privilege_validate = TRUE;
						}
						else
						{
							$privilege_validate = FALSE;
							Kohana::log('info', 'Role privilege update failed');
						}
					}
				}
				else
				{
					// Debug
					Kohana::log('debug', 'Creating new privilege row for the role');
					
					$role_privilege = new Dashboard_Role_Privileges_Model();

					if ($role_privilege->validate($privileges))
					{
						// Success! Save
						$role_privilege->save();

						$privilege_validate = TRUE;
					}
				}

				// Build output JSON string
				$output_json = json_encode(array(
					'success' => $privilege_validate,
					'message' => ($privilege_validate)
						? Kohana::lang('ui_huduma.dashboard_role_saved')
						: Kohana::lang('ui_main.validation_error')
				));
				
			}
			else
			{
				// Validation failed
				$output_json = json_encode(array(
					'success' => FALSE,
					'message' => Kohana::lang('ui_main.validation_error').'<div>'.$data->errors().'<div>'
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
		$dialog_form = array(
			'id' => '',
			'name' => '',
			'description' => '',
			'agency_id' => ''
		);

		// To hold the privileges for the selected role
		$privileges_form = array(
			'static_entity_id' => '',
			'boundary_id' => '',
			'category_id' => ''
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
			$selected_role = new Dashboard_Role_Model($dashboard_role_id);

			// Setup the form fields for the dialog
			$dialog_form = array(
				'id' => $dashboard_role_id,
				'name' => $selected_role->name,
				'description' => $selected_role->description,
				'agency_id' => $selected_role->agency_id
			);

			$privileges = $this->db
					->from('dashboard_role_privileges')
					->where('dashboard_role_id', $dashboard_role_id)
					->get();

			// Log
			Kohana::log('info', sprintf('Found %s privileges for current role', count($privileges)));

			// Fetch privilege calues into $privileges_form
			if (count($privileges) == 1)
			{
				foreach ($privileges as $privilege)
				{
					// Set the current privilege values for the role
					Kohana::log('debug', sprintf('Static entity %d', $privilege->dashboard_role_id));

					$privileges_form = array(
						'static_entity_id' => $privilege->static_entity_id,
						'boundary_id' => $privilege->boundary_id,
						'category_id' => $privilege->category_id
					);
				}

			}

			unset ($privileges);

			// Get the category for which the selected agency belongs to
			if ($selected_role->agency_id != 0)
			{
				$agency_category = ORM::factory('agency', $selected_role->agency_id)->category_id;
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

		// Set the content for the view
		$this->template->dialog_form = $dialog_form;
		$this->template->agencies = $agencies;
		$this->template->categories = $categories;
		$this->template->category_container_css = $category_container_css;
		$this->template->entities = $entities;
		$this->template->boundaries = array();
		$this->template->privileges = $privileges_form;

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
