<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Service Provider controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Alert Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Serviceproviders_Controller extends Admin_Controller {

    public function index()
    {
        $this->template->content = new View('admin/serviceproviders');

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
            $post->add_rules('service_provider_id', 'required', 'numeric');

            // Test if the validation rules passed
            if ($post->validate())
            {
                if ($post->action == 'd')
                {
                    // Delete each selected service provider from the database
                    foreach ($post->service_provider_id as $item)
                    {
                        ORM::factory('service_provider')->delete($item);
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
            'total_items' => ORM::factory('service_provider')->count_all()
        ));
        
        // Get all the service providers
        $service_providers = ORM::factory('service_provider')
                                ->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_action = $form_action;
        $this->template->content->service_providers = $service_providers;
        $this->template->content->pagination = $pagination;
        
        // Total service providers
        $this->template->content->total_items = $pagination->total_items;

        // Javascript header
        $this->tempalte->js = new View("js/serviceproviders_js");
    }


    /**
     * Edit a service provider
     *
     * @param int $service_provider_id
     */
    public function edit($service_provider_id = FALSE, $saved = FALSE)
    {
        // Set the view for editing the service provider
        $this->template->content = new View("admin/serviceprovider_edit");
        
        // Set up and initalize form fields
        $form = array(
            'service_provider_id' => '',
            'provider_name' => '',
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
        $service_provider = "";

        // Check if the form has been submitted
        if ($_POST)
        {
            // Initialize the validation factory
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Validation rules
            $post->add_rules('provider_name', 'required');
            $post->add_rules('category_id', 'required', 'numeric');
            $post->add_rules('description', 'required');

            // Add callbacks to check existence of parent ids
            $post->add_callbacks('parent_id', array($this, 'parent_id_check'));
            $post->add_callbacks('category_id', array($this, 'category_id_check'));
            $post->add_callbacks('boundary_id', array($this, 'boundary_id_check'));

            // Check if the validation rules have held up
            if ($post->validate())
            {
                $service_provider = new Service_Provider_Model($service_provider_id);

                // Set the service provider properties
                $service_provider->provider_name = $post->provider_name;
                $service_provider->description = $post->description;
                $service_provider->category_id = $post->category_id;
                $service_provider->parent_id = $post->parent_id;
                $service_provider->boundary_id = $post->boundary_id;
                $service_provider->creation_date = date("Y-m-d H:i:s");

                // Save to the database
                $service_provider->save();

                $service_provider_id = $service_provider->id;

                // Clear the form values
                array_fill_keys($form, '');

                // SAVE & CLOSE
                if ($post->save == 1)   // Save but don't close
                {
                    // Redirect to the index page of this controller
                    url::redirect('/admin/serviceproviders/edit/'.$service_provider->id.'/saved');
                }
                else
                {
                    url::redirect('admin/serviceproviders/');
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
            // Check if the service provider id has been set, load data
            if ($service_provider_id)
            {
                // Retrieve current service provider
                $service_provider = ORM::factory('service_provider', $service_provider_id);

                if ($service_provider->loaded == true)
                {
                    // Set the form values
                    $form = array(
                        'service_provider_id' => $service_provider->id,
                        'provider_name' => $service_provider->provider_name,
                        'description' => $service_provider->description,
                        'category_id' => $service_provider->category_id,
                        'parent_id' => $service_provider->parent_id,
                        'boundary_id' => $service_provider->boundary_id
                    );
                }
            }
        }
        
        // Get the list of service providers except the one in $service_provider
        $service_provider_list = ORM::factory('service_provider')
                                    ->where(array('parent_id' => '0'))
                                    ->select_list('id', 'provider_name');
        
        // Get the list of categories
        $categories_list = ORM::factory('category')
                            ->where('parent_id', '0')
                            ->select_list('id', 'category_title');

        // List of administrative boundaries
        $admin_boundaries = ORM::factory('boundary')
                                ->select_list('id', 'boundary_name');

        $service_provider_list[0] =  "-- Top Level Service Provider ---";
        
        // Put  "--- Top Level Service Provider ---" at the top of the list
        ksort($service_provider_list);
        
        // Set the content for the view
        $this->template->content->service_provider_id = $service_provider_id;
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->categories = $categories_list;
        $this->template->content->service_providers = $service_provider_list;
        $this->template->content->administrative_boundaries = $admin_boundaries;

        // Javascript header
        $this->template->js = new View('js/serviceprovider_edit_js');
    }

    /**
     * Shows the metadata about a service provider, no. of service officers, issues assigned, ticket statistics
     *
     * @param int $service_provider_id Database id of the service provider to be view
     */
    public function view($service_provider_id)
    {
        $this->template->content = new View("admin/serviceprovider_view");
        
        // Retrive the basic info from the database
        $service_provider = ORM::factory('service_provider', $service_provider_id);
        
        // Show basic information about the service provider
        $this->template->content->service_provider = $service_provider;
        
        // Show officers for this service provider
        
        // Ticket queue information - statistics
    }
    
    /**
     * Show the list of officers for the service provider specified in @param $service_provider_id
     * 
     * @param int $service_provider_id 
     */
    public function officers($service_provider_id)
    {
        $this->template->content = new View('admin/serviceprovider_officers');

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
            $post->add_rules('service_provider_officer_id', 'required', 'numeric');

            // Test is the validation has passed
            if ($post->validate())
            {
                if ($post->action == 'd')
                {
                    // Get each selected officer and delete from the database
                    foreach ($post->service_provider_officer_id as $item)
                    {
                        // Delete
                        ORM::factory('service_provider_officer')->delete($item);
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

        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => Kohana::config('settings.items_per_page_admin'),
            'total_items' => ORM::factory('service_provider_officer')
                                ->where('service_provider_id', $service_provider_id)
                                ->count_all()
        ));

        // Get the officers for the service provider
        $officers = ORM::factory('service_provider_officer')
                                ->where('service_provider_id', $service_provider_id)
                                ->find_all();


        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->service->provider = ORM::factory('service_provider', $service_provider_id);
        $this->tempalte->content->officers = $officers;
        $this->template->content->pagination = $pagination;

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript header
        $this->template->js = new View("js/serviceprovider_officers_js");
    }

    /**
     * Adds a service provider officer
     */
    public function officer($officer_id = FALSE)
    {
        
    }

    /**
     * Displays the tickets with status @param $ticket_status for the service provider specified in @param $service_provider_id
     *
     * @param int $service_provider_id
     * @param int $ticket_status
     */
    public function tickets($service_provider_id, $ticket_status = FALSE)
    {
//        $this->template->title = "Tickets";
        $this->template->content = new View('admin/serviceprovider_tickets');

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
            'total_items' => Service_Provider_Model::tickets($service_provider_id, $ticket_status)->count_all()
        ));

        // Get the tickets for the service provider
        $tickets = Service_Provider_Model::tickets($service_provider_id, $ticket_status);

        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->service->provider = ORM::factory('service_provider', $service_provider_id);
        $this->template->content->ticket = $tickets;
        $this->tempalte->content->pagination = $pagination;

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript header
        $this->template->js = new View("admin/serviceprovider_tickets_js");
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
     * Validates the parent id of the service provider
     * 
     * @param Validation $post
     */
    public function parent_id_check(Validation $post)
    {
        // Check if a validation error for the parent id already exists
        if (array_key_exists('parent_id', $post->errors()))
            return;

        $parent_id = $post->parent_id;
        $service_provider_id = $post->service_provider_id;

        // Check if it is a parent service provider
        if ($parent_id == 0)
            return;

        // Check if the parent id exists
        $parent_exists = ORM::factory('service_provider', $parent_id)->loaded;

        if ( ! $parent_exists)
        {
            $post->add_error('parent_id', 'The specified parent service provider does not exist');
        }

        // Check if the parent and the service provider id are the same
        if ( ! empty($service_provider_id) AND $service_provider_id == $parent_id)
        {
            $post->add_error('parent_id', 'The service provider is the same as the parent');
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
        
}
?>