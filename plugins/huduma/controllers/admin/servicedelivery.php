<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Service delivery controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Service Delivery Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */

class Servicedelivery_Controller extends Admin_Controller {

    /**
     * Landing page: Displays the list of admin boundaries
     */
	public function index()
	{
		$this->template->content = new View('admin/boundaries');
		$this->template->content->title = Kohana::lang('ui_huduma.administrative_boundaries');

		// setup and initialize form field names
		$form = array(
			'boundary_id' => '',
			'boundary_name' => '',
			'boundary_type' => '',
			'parent_id' => '',
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$boundary_array = array();

		$boundary_id = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			if ($_POST['action'] == 'a')	// Add/Update
			{
				// Manually extract the $_POST data
				$boundary_data = arr::extract($_POST, 'boundary_name', 'boundary_type', 'parent_id');
				
				// Boundary model instance for the operation
				$boundary = (isset($_POST['boundary_id']) AND Boundary_Model::is_valid_boundary($_POST['boundary_id']))
						? ORM::factory('boundary', $_POST['boundary_id'])
						: new Boundary_Model();
				
				// TODO: Check for upload file
				if ($boundary->validate($boundary_data))
				{
					// Success! SAVE
					$boundary->save();
					
					$form_saved = TRUE;
					$form_action = Kohana::lang('ui_admin.added_edited');
					
					// Clear the errors and form fields
					array_fill_keys($form, '');
					$errors = $form;
				}
				else
				{
					// Overwrite forms and errors
					$form = arr::overwrite($form, $boundary_data->as_array());;
					$errors = arr::overwrite($errors, $boundary_data->errors('boundaries'));
					
					// Turn on form error
					$form_error = TRUE;
					$form_saved = FALSE;
				}
			}
			elseif ($_POST['action'] == 'd')	// Delete
			{
				foreach($_POST['boundary_id'] as $boundary_id)
				{
					// Delete the boundary item
					ORM::factory('boundary', $boundary_id)->delete();

					// TODO: Purge uploads too
				}

				// Success
				$form_saved = TRUE;

				$form_action = Kohana::lang('ui_admin.deleted');
			}
		}	// END if $_POST

		// No. of items to display per page
		$items_per_page = (int)Kohana::config('settings.items_per_page_admin');
		
		Kohana::log('info', sprintf('Items per page %s', $items_per_page));
		
		// Setup pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $items_per_page,
			'total_items'    => ORM::factory('boundary')->count_all()
		));

		// Boundaries
		$boundaries = ORM::factory('boundary')
						->orderby('id', 'asc')
						->find_all($items_per_page, $pagination->sql_offset);

		// Boundary types
		// TODO - Fethc this from a language file
		$boundary_types = array('1' => 'County', '2' => 'Constituency');
		
		// Get the parent boundaries
		$parent_boundaries = Boundary_Model::get_parent_boundaries();
		$parent_boundaries[0] = "---".Kohana::lang('ui_huduma.top_level_boundary')."---";
		ksort($parent_boundaries);

		// Output
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;

		$this->template->content->boundaries = $boundaries;
		$this->template->content->boundary_types = $boundary_types;
		$this->template->content->parent_boundaries = $parent_boundaries;

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Javascript Header
		$this->template->js = new View('js/boundary_js');
	}
}