<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Agencies controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Agencies Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Agencies_Controller extends Admin_Controller {

	public function index()
	{
		$this->template->content = new View('admin/agencies');

		// Form submission status flags
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
        
		// Check if the form has been submitted, if so set up validation
		if ($_POST)
		{
			// Setup validation
			$post = Validation::factory($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by some check
			$post->add_rules('action', 'required', 'alpha', 'length[1,1]');
			$post->add_rules('agency_id', 'required', 'numeric');

			// Test if the validation rules passed
			if ($post->validate())
			{
				if ($post->action == 'd')
				{
					// Delete each selected service provider from the database
					foreach ($post->agency_id as $item)
					{
						ORM::factory('agency')->delete($item);
					}

					// Success
					$form_saved = TRUE;

					// Set the form action
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
			else 	// Validation failed
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Turn on the form error
				$form_error = TRUE;
			}
			//> END validation
		}
        
		// Pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => Kohana::config('settings.items_per_page_admin'),
			'total_items' => ORM::factory('agency')->count_all()
		));
        
		// Get all the agencies
		$agencies = ORM::factory('agency')
					->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_action = $form_action;
		$this->template->content->agencies = $agencies;
		$this->template->content->pagination = $pagination;

		// Total service providers
		$this->template->content->total_items = $pagination->total_items;

		// Javascript header
		$this->tempalte->js = new View("js/agencies_js");
	}


	/**
	 * Edit an agency
	 *
	 * @param int $agency_id
	 */
	public function edit($agency_id = FALSE, $saved = FALSE)
	{
		// Set the view for editing the service provider
		$this->template->content = new View("admin/agencies_edit");

		// Set up and initalize form fields
		$form = array(
			'agency_id' => '',
			'agency_name' => '',
			'description' => '',
			'category_id' => '',
			'parent_id' => '',
			'boundary_id' => ''
		);

		// Copy the form as errors so that the errors are stored with the keys corresponding to the form fields names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved')? TRUE : FALSE;

		// To hold the service provider reference
		$agency = (Agency_Model::is_valid_agency($agency_id)) ? new Agency_Model($agency_id) : "";

		// Check if the form has been submitted
		if ($_POST)
		{
			// Check for Agency_Model instance
			if (empty($agency))
			{
				$agency = new Agency_Model();
			}
			
			// Manually extract the data to be passed on for validation
			$data = arr::extract($_POST, 'agency_name', 'category_id', 'description', 'boundary_id', 'parent_id');

			// Validate
			if ($agency->validate($data))
			{
				// SUCCESS! Save
				$agency->creation_date = date("Y-m-d H:i:s");

				// Save to the database
				$agency->save();

				// Clear the form values
				array_fill_keys($form, '');

				// SAVE & CLOSE
				if ($_POST['save'] == 1)   // Save but don't close
				{
					// Redirect to the index page of this controller
					url::redirect('/admin/agencies/edit/'.$agency->id.'/saved');
				}
				else
				{
					url::redirect('admin/agencies/');
				}
			}
			// Validation failed therefore show errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $data->as_array());

				// Populate the error fields if any
				$errors = arr::overwrite($errors, $data->errors('edit'));

				$form_error = TRUE;
			}
		}
		else
		{
			// Check if the agency id has been set, load data
			if ( ! empty($agency))
			{
				// Set the form values
				$form = array(
					'agency_id' => $agency->id,
					'agency_name' => $agency->agency_name,
					'description' => $agency->description,
					'category_id' => $agency->category_id,
					'parent_id' => $agency->parent_id,
					'boundary_id' => $agency->boundary_id
				);
			}
		}
        
		// Get the list of agencies except the one in $agency
		$agencies_list = ORM::factory('agency')
							->where(array('parent_id' => '0'))
							->select_list('id', 'agency_name');

		// Get the list of categories
		$categories_list = ORM::factory('category')
							->where('parent_id', '0')
							->select_list('id', 'category_title');

		// Feth the list of administrative boundaries
		$admin_boundaries = Boundary_Model::get_boundaries_dropdown(TRUE);

		$admin_boundaries[0] = "-- National Level --";

		$agencies_list[0] =  "-- Top Level Agency ---";

		// Put  "--- Top Level Service Provider ---" at the top of the list
		ksort($agencies_list);
		ksort($admin_boundaries);
        
		// Set the content for the view
		$this->template->content->agency_id = $agency_id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->categories = $categories_list;
		$this->template->content->agencies = $agencies_list;
		$this->template->content->administrative_boundaries = $admin_boundaries;

		// Javascript header
		$this->template->js = new View('js/agencies_edit_js');
	}

    /**
     * Displays the tickets with status @param $ticket_status for the service provider specified in @param $agency_id
     *
     * @param int $agency_id
     * @param int $ticket_status
     */
    public function tickets($agency_id, $ticket_status = FALSE)
    {
//        $this->template->title = "Tickets";
        $this->template->content = new View('admin/agency_tickets');

        // Form submission status flags
        $form_error = FALSE;
        $form_saved  =TRUE;
        $form_action = "";

        // Has the form been submitted, if so set up validation
        if ($_POST)
        {
            // Set up validation
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add validation rules, the input field, followed by some checks, in that order
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            $post->add_rules('ticket_id', 'required', 'numeric');

            // Has validation passsed
            if ($post->validate())
            {
                if ($post->action == 'd') // Delete ticket
                {
                    // Get the ticket id
                    $ticket_id = $post->ticket_id;

                    // Delete the ticket history
                    ORM::factory('ticket_history')->where('ticket_id', $ticket_id)->delete_all();

                    // Delete the ticket
                    ORM::factory('ticket')->delete($post->ticket_id);

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.deleted');
                }
            }
            else // Validation failed
            {
                // Repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // Turn on the form error
                $form_error = TRUE;
            }
        }

        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => Kohana::config('settings.items_per_page_admin'),
            'total_items' => Service_Provider_Model::tickets($agency_id, $ticket_status)->count_all()
        ));

        // Get the tickets for the service provider
        $tickets = Service_Provider_Model::tickets($agency_id, $ticket_status);

        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->service->provider = ORM::factory('agency', $agency_id);
        $this->template->content->ticket = $tickets;
        $this->tempalte->content->pagination = $pagination;

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript header
        $this->template->js = new View("js/agency_tickets_js");
    }

	/**
	 * Displays the landing page for this controller
	 */
	public function types()
	{
		$this->template->content = new View('admin/agency_types');
		$this->template->content->title = Kohana::lang('ui_huduma.agency_types');

		// setup and initialize form field names
		$form = array(
			'agency_type_id' => '',
			'type_name' => '',
			'short_name' => '',
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$boundary_array = array();

		$agency_type_id = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Check actions
			if ($_POST['action'] == 'a')	// Add/Update
			{
				// Manually extract the $_POST data
				$agency_data = arr::extract($_POST, 'type_name', 'short_name');
				
				Kohana::log('debug', Kohana::debug($agency_data));
				
				// Boundary model instance for the operation
				$agency_type = (isset($_POST['agency_type_id']) AND Agency_Type_Model::is_valid_agency_type($_POST['agency_type_id']))
						? ORM::factory('agency_type', $_POST['agency_type_id'])
						: new Agency_Type_Model();
														
				// TODO: Check for upload file
				if ($agency_type->validate($agency_data))
				{
					// Success! SAVE
					$agency_type->save();
					
					$form_saved = TRUE;
					$form_action = Kohana::lang('ui_admin.added_edited');
					
					// Clear the errors and form fields
					array_fill_keys($form, '');
					$errors = $form;
				}
				else
				{
					Kohana::log('debug', 'Validation failed');
					
					// Overwrite forms and errors
					$form = arr::overwrite($form, $agency_data->as_array());;
					$errors = arr::overwrite($errors, $agency_data->errors());
					
					// Turn on form error
					$form_error = TRUE;
					$form_saved = FALSE;
				}
			}
			elseif ($_POST['action'] == 'd')	// Delete
			{
				foreach($_POST['agency_type_id'] as $agency_type_id)
				{
					// Delete the boundary item
					ORM::factory('agency_type', $agency_type_id)->delete();

					// TODO: Purge uploads too
				}

				// Success
				$form_saved = TRUE;

				$form_action = Kohana::lang('ui_admin.deleted');
			}
		}	// END if $_POST

		// No. of items to display per page
		$items_per_page = (int)Kohana::config('settings.items_per_page_admin');
		
		// Setup pagination
		$pagination = new Pagination(array(
			'query_string' => 'page',
			'items_per_page' => $items_per_page,
			'total_items'    => ORM::factory('agency_type')->count_all()
		));

		// agency_types
		$agency_types = ORM::factory('agency_type')
						->orderby('id', 'asc')
						->find_all($items_per_page, $pagination->sql_offset);

		
		$this->template->colorpicker_enabled = TRUE;
		
		// Set content view
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;

		$this->template->content->agency_types = $agency_types;

		// Locale (Language) Array
		$this->template->content->locale_array = Kohana::config('locale.all_languages');

		// Javascript Header
		$this->template->js = new View('js/agency_types_js');
	}	
}

?>