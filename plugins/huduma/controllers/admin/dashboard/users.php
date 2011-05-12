<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Users controller
 *
 * This controller manages creation of users of the frontend dashboards
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Users
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Users_Controller extends Admin_Controller {

	/**
	 * Loads the landing page for this controller
	 */
	public function index()
	{
		// Load the view for this page
		$this->template->content = new View('admin/dashboard/users');

		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		// TODO: Validation for form actions such as "delete"

		// Get the no. of items to display per page
		$items_per_page = (int) Kohana::config('settings.items_per_page_admin');

		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $items_per_page,
			'total_items' => ORM::factory('dashboard_user')->count_all()
		));

		// Get the list of users for the current page
		$users  = ORM::factory('dashboard_user')
					->find_all($items_per_page, $pagination->sql_offset);

		// Set the content for the view
		$this->template->content->pagination = $pagination;
		$this->template->content->dashboard_users = $users;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Total no. of dashboard users
		$this->template->content->total_items = $pagination->total_items;

		// TODO Javascript header
	}

	/**
	 * Displays the form/page for creating/editing a dashboard user
	 *
	 * @param int $dashboard_user_id
	 * @param boolean $save
	 */
	public function edit($dashboard_user_id = FALSE, $save = FALSE)
	{
		// Load the view for this page
		$this->template->content = new View('admin/dashboard/user_edit');

		// Set up and initialize the form fields
		$form = array(
			'dasbhoard_user_id' => '',
			'name' => '',
			'username' => '',
			'email' => '',
			'password' => '',
			'confirm_password' => '',
			'dashboard_role_id' => '',
			'is_active' => ''
		);

		// Copy the form as errors so that the errors are stored with keys corresponding to the form fields
		$errors = $form;
		$form_error = FALSE;
		$form_saved  = ($save == 'saved') ? TRUE : FALSE;
		
		// To hold reference to the user in @param $user_id
		$current_user = NULL;

		// Check if the form has been submitted
		if ($_POST)
		{
			$current_user = new Dashboard_User_Model($dashboard_user_id);

			// Explicity specify the fields to be extracted from post
			// Avoids invalid/unwanted properties from being during validation by the model
			$data = arr::extract($_POST, 'name', 'username', 'email', 'password',
					'confirm_password', 'is_active', 'dashboard_role_id', 'save');

			// Add the @param $dashboard_user_id if set
			if (Dashboard_User_Model::is_valid_dashboard_user($dashboard_user_id))
			{
				$data = array_merge($data, array('id' => $dashboard_user_id));
			}

			Kohana::log('debug', sprintf('Dashboard role %d', $data['dashboard_role_id']));
			
			if ($current_user->validate($data))
			{
				// Successfully validated! Save
				$current_user->save();

				// No form error
				$form_error = FALSE;

				// Save succeeded
				$form_saved = TRUE;

				// Clear form values
				array_fill_keys($form, '');

				if ($data->save == 1)
				{
					// Stay on current page
					url::redirect(sprintf('admin/dashboard/users/edit/%d/%s', $current_user->id, 'saved'));
				}
				else
				{
					// Redirect to the list of users
					$this->index();
				}
			}
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $data->as_array());
				$errors = arr::overwrite($errors, $data->errros());
				
				// Turn on the form error
				$form_error = TRUE;
				$form_saved = FALSE;
			}

		}
		else
		{
			// Check if the the $user_id has been set
			if ($dashboard_user_id AND Dashboard_User_Model::is_valid_dashboard_user($dashboard_user_id))
			{
				Kohana::log('debug', sprintf('Validation of user %d succeeded', $dashboard_user_id));

				// Set the current dashboard user
				$current_user = new Dashboard_User_Model($dashboard_user_id);

				// Set the form values
				$form = array(
					'id' => $dashboard_user_id,
					'name' => $current_user->name,
					'username' => $current_user->username,
					'email' => $current_user->email,
					'dashboard_role_id' => $current_user->dashboard_role_id,
					'is_active' => $current_user->is_active
				);
			}
			else
			{
				Kohana::log('debug', 'User validation failed');
			}
		}

		$dashboard_roles = Dashboard_Role_Model::get_dropdown_roles();
		$dashboard_roles[0] = "---".Kohana::lang('ui_huduma.select_role')."---";
		ksort($dashboard_roles);

		// Set the content for the view
		$this->template->content->dashboard_user_id = $dashboard_user_id;
		$this->template->content->form = $form;
		$this->template->content->dashboard_roles = $dashboard_roles;
		$this->template->content->errors = $errors;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_error = $form_error;

		// Javascript header
		$this->template->js = new View('js/user_edit_js');
	}
}
?>
