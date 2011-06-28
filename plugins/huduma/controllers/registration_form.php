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
		
		if ($_POST)
		{
			// Form error tracking
			$form_error = FALSE;
			
			// @todo - This make use of the role based system code
			// Automatically create a role and assign it to the user
			// Manually extract the data to be validated
			$data = arr::extract($_POST, 'first_name', 'last_name');
			
			// Verify that either a constituency or county has been specified
			if ( ! empty($_POST['constituency_id']))
			{
				$data = array_merge($data, array('boundary_id' => $_POST['constituency_id']));
			}
			elseif ( empty($_POST['constituency_id']) AND ! empty($_POST['county_id']))
			{
				$data = array_merge($data, array('boundary_id' => $_POST['county_id']));
			}
			else
			{
				$form_error = TRUE;
			}
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