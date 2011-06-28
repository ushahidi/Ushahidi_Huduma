<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Handles the user registration form
 *
 * @author Ushahidi Dev Team - http://ushahidi.com
 * @copyright Huduma - http://huduma.info
 * @license
 */
class Registration_Form_Controller extends Frontend_Controller {
	
	public function index()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// Load the registration form
		$registration_form = new View('huduma/registration_form');
		
		// Fetch the categories
		$categories = Category_Model::get_dropdown_categories();
		$categories[0] = "---".Kohana::lang('ui_huduma.select_category')."---";
		ksort($categories);
		$registration_form->categories = $categories;
		
		// Fetch the parent boundaries - counties only
		$counties = Boundary_Model::get_parent_boundaries();
		$counties[0] = "---".Kohana::lang('ui_huduma.select_county')."---";
		ksort($counties);
		$registration_form->counties = $counties;
		
		$registration_form->facility_types = array('0' => "---".Kohana::lang('ui_huduma.select_facility_type')."---");
		$registration_form->facilities = array('0' => "---".Kohana::lang('ui_huduma.select_facility')."---");
		
		// Display the reigstration form
		print $registration_form;
		
	}
	
	/**
	 * Processes the subnitted data
	 */
	public function register()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		header("Content-type: application/json; charset=utf-8");
		
		if ($_POST)
		{
			// Manually extract the data to be validated
			$username_data = arr::extract($_POST, 'name', 'email', 'phone_number');
			
			// Exrtact the data for the role
			$role_data = arr::extract($_POST, 'category_id', 'boundary_id', 'static_entity_id');
			
			// Check if the role exists
			$role = Dashboard_Role_Model::role_exists($role_data);
			if ($role == FALSE)
			{
				// To store the role name
				$role_name = 'Role';
				
				// Set the role name and description
				if ( ! empty($role_data['category_id']))
				{
					$role_name .= '_'.ORM::factory('category', $role_data['category_id'])->category_title;
				}
				
				if ( ! empty($role_data['boundary_id']))
				{
					$role_name .= '_'.ORM::factory('boundary', $role_data['boundary_id'])->boundary_name;
				}
				
				if ( ! empty($role_data['static_entity_id']))
				{
					$role_name = 'Role_'.ORM::factory('static_entity', $role_data['static_entity_id'])->entity_name;
				}
				
				// Trim the role name to 35 characters
				$role_name = substr($role_name, 0, 35);
				
				// Set the role name and description
				// Disable closing of issues for the role
				$role_data = array_merge($role_data, array(
					'name' => $role_name, 
					'description' => $role_name, 
					'agency_id' => 0, 
					'can_close_issue' => 0,
					'in_charge' => $_POST['in_charge']
				));
				
				// Debug
				// Kohana::log('debug', Kohana::debug($role_data));
				
				// Create a new role
				$role = new Dashboard_Role_Model();
				if ($role->validate($role_data))
				{
					// Save the role
					$role->save();
				}
				else
				{
					print json_encode(array(
						'success' => FALSE,
						'message' => $role_data->errors()
					));
					
					exit(1);
				}
			}
			
			// Add the role id to the username data and also set the username to inactive
			$user_password = text::random('alnum');
			$extra_properties = array(
				'username' => $username_data['email'],
				'password' => $user_password,
				'confirm_password' => $user_password,
				'dashboard_role_id' => $role->id, 
				'is_active' => 0,
			);
			
			// Add the extra properties to the username
			$username_data = array_merge($username_data, $extra_properties);
			
			// Validate and save the user
			$dashboard_user = new Dashboard_User_Model();
			if ($dashboard_user->validate($username_data))
			{
				// Success! Save
				$dashboard_user->save();
				
				// Send information confirmation via SMS/email
				
				// Return success message
				print json_encode(array(
					'success' => TRUE,
					'message' => Kohana::lang('ui_huduma.registration_successful')
				));
			}
			else
			{
				// Delete the role
				$role->delete();
				
				// Show error message
				print json_encode(array(
					'success' => FALSE,
					'message' => $username_data->errors()
				));
			}
			
		}
		else
		{
			print json_encode(array('success' => FALSE));
		}
	}
	
	/**
	 * Gets the facility types of the specified category
	 */
	public function get_facility_types($category_id)
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$facility_types = Static_Entity_Type_Model::get_entity_types_dropdown($category_id);
		if ($facility_types)
		{
			$html_str = "<option value=\"0\">---".Kohana::lang('ui_huduma.select_facility_type')."---</option>";
			foreach ($facility_types as $key => $value)
			{
				$html_str .= "<option value=\"".$key."\">".$value."</option>";
			}
			print $html_str;
		}
		else
		{
			print "";
		}
	}
	
	/**
	 * Gets the facilities of a specified type
	 */
	public function get_facilities($facility_type_id, $boundary_id)
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$facilities = Static_Entity_Model::get_entities_by_type($facility_type_id, $boundary_id);
		if ($facilities)
		{
			$html_str = "<option value=\"0\">---".Kohana::lang('ui_huduma.select_facility')."---</option>";
			foreach ($facilities as $key => $value)
			{
				$html_str .= "<option value=\"".$key."\">".$value."</option>";
			}
			print $html_str;
		}
		else
		{
			print "";
		}
	}
	
	/**
	 * Gets the list of constituencies
	 */
	public function get_constituencies($county_id)
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		$constituencies = Boundary_Model::get_child_boundaries($county_id);
		if ($constituencies)
		{
			$html_str = "<option value=\"0\">".Kohana::lang('ui_huduma.select_constituency')."</option>";
			foreach ($constituencies as $key => $value)
			{
				$html_str .= "<option value=\"".$key."\">".$value."</option>";
			}
			print $html_str;
		}
		else
		{
			print "";
		}
	}
}
?>