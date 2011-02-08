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
            else // Validation failed
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
        $agency = "";

        // Check if the form has been submitted
        if ($_POST)
        {
            // Initialize the validation factory
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Validation rules
            $post->add_rules('agency_name', 'required');
            $post->add_rules('category_id', 'required', 'numeric');
            $post->add_rules('description', 'required');

            // Add callbacks to check existence of parent ids
//            $post->add_callbacks('parent_id', array($this, 'parent_id_check'));
            $post->add_callbacks('category_id', array($this, 'category_id_check'));
            $post->add_callbacks('boundary_id', array($this, 'boundary_id_check'));

            // Check if the validation rules have held up
            if ($post->validate())
            {
                $agency = new Agency_Model($agency_id);

                // Set the service provider properties
                $agency->agency_name = $post->agency_name;
                $agency->description = $post->description;
                $agency->category_id = $post->category_id;
                $agency->parent_id = $post->parent_id;
                $agency->boundary_id = $post->boundary_id;
                $agency->creation_date = date("Y-m-d H:i:s");

                // Save to the database
                $agency->save();

                $agency_id = $agency->id;

                // Clear the form values
                array_fill_keys($form, '');

                // SAVE & CLOSE
                if ($post->save == 1)   // Save but don't close
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
                $form = arr::overwrite($form, $post->as_array());

                // Populate the error fields if any
                $errors = arr::overwrite($errors, $post->errors('edit'));

                $form_error = TRUE;
            }
        }
        else
        {
            // Check if the agency id has been set, load data
            if ($agency_id)
            {
                // Retrieve current agency
                $agency = ORM::factory('agency', $agency_id);

                if ($agency->loaded == true)
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
        }
        
        // Get the list of agencies except the one in $agency
        $agencies_list = ORM::factory('agency')
                                    ->where(array('parent_id' => '0'))
                                    ->select_list('id', 'agency_name');
        
        // Get the list of categories
        $categories_list = ORM::factory('category')
                            ->where('parent_id', '0')
                            ->select_list('id', 'category_title');

        // List of administrative boundaries
        $admin_boundaries = ORM::factory('boundary')
                                ->select_list('id', 'boundary_name');

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
     * Show the list of officers for the service provider specified in @param $agency_id
     * 
     * @param int $agency_id
     */
    public function staff($agency_id = FALSE)
    {
        $this->template->content = new View('admin/agency_staff');

        // Form submission status flags
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";

        // Has the form been submitted, set up validation
        if ($_POST)
        {
            // Set up validation
            $post =  Validation::factory($_POST);

            // Add some filters
            $post->add_filter('trim', TRUE);

            // Add some rules, the input field, followed by some checks in that order
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            $post->add_rules('agency_staff_id', 'required', 'numeric');

            // Test is the validation has passed
            if ($post->validate())
            {
                if ($post->action == 'd')
                {
                    // Get each selected officer and delete from the database
                    foreach ($post->service_provider_officer_id as $item)
                    {
                        // Delete
                        ORM::factory('agency_staff')->delete($item);
                    }

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.deleted');
                }
            }
            else // Validation has failed
            {
                // Repopulate ethe form fields
                $form = arr::overwrite($form, $post->as_errors());

                // Turn on the form error
                $form_error = TRUE;
            }
            //> END validation

        }

        // Generate the where clause
        $where_clause = ($agency_id)? 'agency_id = '.$agency_id : '1=1';

        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => Kohana::config('settings.items_per_page_admin'),
            'total_items' => ORM::factory('agency_staff')
                                ->where($where_clause)
                                ->count_all()
        ));

        // Get the officers for the service provider
        $staff = ORM::factory('agency_staff')
                                ->where($where_clause)
                                ->find_all();


        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->staff = $staff;
        $this->template->content->pagination = $pagination;

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript header
//        $this->template->js = new View("js/agency_staff_js");
    }

    public function edit_staff($agency_staff_id = FALSE, $saved = FALSE)
    {
        $this->template->content = new View('admin/agency_staff_edit');
        $form = array(
            'agency_staff_id' => '',
            'agency_id' => '',
            'full_name' => '',
            'email_address' => '',
            'phone_number' => ''
        );

        // Copy the form as errors so that the errors are stored using the same keys as the input fields
        $errors = $form;
        
        $form_error = FALSE;
        $form_saved = ($saved == 'saved')? TRUE : FALSE;
        $form_action = "";
        
        // Check if the form has been submitted, set up validatino
        if ($_POST)
        {
            // Set up validation
            $post = Validation::factory($_POST);
            
            // Add some filters
            $post->pre_filter('trim', TRUE);
            
            // Add some rules, the input field, followed by some checks in that order
            if ($post->action == 'a')
            {
                $post->add_rules('full_name', 'required');
                $post->add_rules('email_address', 'required', 'email');
                $post->add_rules('agency_id', 'required', 'numeric');

                // Add some callback functions to check existence of foreign keys
                $post->add_callbacks('agency_id', array($this, 'agency_id_check'));
            }
            
            // Validation passed?
            if ($post->validate())
            {
                if ($post->action == 'a')
                {
                    $staff = new Agency_Staff_Model($agency_staff_id);
                    $staff->full_name = $post->full_name;
                    $staff->email_address = $post->email_address;
                    $staff->agency_id = $post->agency_id;
                    $staff->phone_number = $post->phone_number;

                    $staff->save();

                    // Clear the form values
                    array_fill_keys($form, '');

                    if ($post->save == 1)   // Save but don't close
                    {
                        url::redirect('admin/agencies/edit_staff/'.$staff->id.'/saved');
                    }
                    else
                    {
                        url::redirect('admin/agencies/staff');
                    }
                }
            }
            else // Validation failed
            {
                // Populate the form fields
                $form = arr::overwrite($form, $post->as_array());
                
                //Populate the errors, if any
                $errors = arr::overwrite($errors, $post->errors('Edit Staff'));
                
                // Turn on the form error
                $form_error = TRUE;
            }
        }
        else
        {
            // Check if the agency id has been set, load data
            if ($agency_staff_id)
            {
                // Retrieve current agency
                $staff = ORM::factory('agency_staff', $agency_staff_id);

                if ($staff->loaded == true)
                {
                    // Set the form values
                    $form = array(
                        'agency_staff_id' => $staff->id,
                        'full_name' => $staff->full_name,
                        'email_address' => $staff->email_address,
                        'agency_id' => $staff->agency_id,
                        'phone_number' => $staff->phone_number
                    );
                }
            }
            
        }

        // Get the list of agencies
        $agency_array = ORM::factory('agency')->select_list('id', 'agency_name');

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_action = $form_action;
        $this->template->content->agency_array =  $agency_array;
        $this->template->content->agency_staff_id = $agency_staff_id;

        // Javascript Header
        $this->template->js = new View('js/agency_staff_edit_js');
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

//> VALIDATION CALLBACK FUNCTIONS

    /**
     * Checks if the specified category id exists in the database
     *
     * @param Validation $post
     */
    public function category_id_check(Validation $post)
    {
        // Check if the category id error already exists in the validation erros
        if (array_key_exists('category_id', $post->errors()))
            return;

        $category_exists = ORM::factory('category', $post->category_id)->loaded;

        // If not exists, set the error
        if ( ! $category_exists)
        {
            $post->add_error('category_id', 'Invalid category');
        }
    }

    /**
     * Validates the parent id of the agency
     * 
     * @param Validation $post
     */
    public function parent_id_check(Validation $post)
    {
        // Check if a validation error for the parent id already exists
        if (array_key_exists('parent_id', $post->errors()))
            return;

        $parent_id = $post->parent_id;
        $agency_id = $post->agency_id;

        // Check if it is a parent service provider
        if ($parent_id == 0)
            return;

        // Check if the parent id exists
        $parent_exists = ORM::factory('agency', $parent_id)->loaded;

        if ( ! $parent_exists)
        {
            $post->add_error('parent_id', 'The specified parent agency does not exist');
        }

        // Check if the parent and the service provider id are the same
        if ( ! empty($agency_id) AND $agency_id == $parent_id)
        {
            $post->add_error('parent_id', 'The agency is the same as the parent');
        }

    }

    /**
     * Checks if the administrative boundary exists
     * 
     * @param Validation $post
     */
    public function boundary_id_check(Validation $post)
    {
        // Check if a validation error for the admin boundary exists
        if (array_key_exists('boundary_id', $post->errors()))
            return;

        if ($post->boundary_id == 0)
            return;

        // Check if the specified admin boundary exists
        $boundary_exists = ORM::factory('boundary', $post->boundary_id)->loaded;

        if ( ! $boundary_exists)
        {
            $post->add_error('boundary_id', 'Invalid administrative boundary');
        }
    }

    /**
     * Checks if the agency id contained in @param $post exists in the database
     * 
     * @param Validation $post
     */
    public function agency_id_check(Validation $post)
    {
        // Check if a validation error for the agency_id already exists
        if (array_key_exists('agency_id', $post->errors()))
            return;

        // Check if the specified agency id exists
        $agency_exists = ORM::factory('agency', $post->agency_id)->loaded;

        if ( ! $agency_exists)
        {
            $post->add_error('agency_id', 'Invalid service agency');
        }
    }
        
}
?>