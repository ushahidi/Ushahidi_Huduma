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
        $this->template->content = new View('admin/boundary');
        $this->template->content->title = Kohana::lang('ui_servicedelivery.administrative_boundaries');

        // setup and initialize form field names
        $form = array(
            'action' => '',
            'boundary_id'  => '',
            'boundary_type_id' => '',
            'parent_id' => '',
            'boundary_name' => '',
            'boundary_type_name' => ''
        );

        // copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        $boundary_array = array();

        $boundary_id = "";

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite
            // $_POST fields with our own things
            $post = Validation::factory(array_merge($_POST));

            //  Add some filters
            $post->pre_filter('trim', TRUE);

            if ($post->action == 'a')       // Add Action
            {
                // Add some rules, the input field, followed by a list of
                // checks, carried out in order
                $post->add_rules('boundary_name','required','length[3,80]');
                $post->add_rules('boundary_type_id', 'required');
                $post->add_rules('parent_id', 'required', 'numeric');

                // Add callback functions to check existence of foregin keys
                $post->add_callbacks('boundary_type_id', array($this, 'boundary_type_id_check'));
                $post->add_callbacks('parent_id', array($this, 'parent_boundary_id_check'));
            }
            elseif ($post->action == 'd')
            {
                $post->add_rule('boundary_id.*', 'required', 'numeric');
            }

            // Test to see if things passed the rule checks
            if ($post->validate())
            {
                if( $post->action == 'd' ) // Delete Action
                {
                    foreach ($post->boundary_id as $id)
                    {
                        ORM::factory('boundary')->delete($id);
                    }

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang("ui_admin.deleted");

                }
                elseif( $post->action == 'a' ) // Save Action
                { 
                    $boundary = new Boundary_Model($post->boundary_id);
                    $boundary->boundary_name = $post->boundary_name;
                    $boundary->boundary_type_id = $post->boundary_type_id;
                    $boundary->parent_id = $post->parent_id;
                    $boundary->creation_date = date("Y-m-d H:i:s");
                    $boundary->save();

                    // Set the boundary id
                    $boundary_id = $boundary->id;

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang("ui_admin.added_edited");

                    // Clear the form values
                    array_fill_keys($form, '');
                }
            }
            // No! We have validation errors, we need to show the form
            // again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('boundaries'));
                
                $form_error = TRUE;
            }
        }

        // Pagination
        $pagination = new Pagination(array(
                        'query_string' => 'page',
                        'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
                        'total_items'    => ORM::factory('boundary')->where('parent_id',0)->count_all()
        ));

        // Boundaries
        $boundaries = ORM::factory('boundary')
                            ->orderby('id', 'ASC')
                            ->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

        // Parent boundaries
        $parent_boundaries = ORM::factory('boundary')
                                ->where( (!$boundary_id) ? array('id > ' => 0) : array('parent_id !=' => $boundary_id))
                                ->select_list('id', 'boundary_name');

        // Boundary types
        $boundary_types = ORM::factory('boundary_type')
                            ->select_list('id', 'boundary_type_name');

        // Append the boundary type name to the parent boundary
        foreach ($boundaries as $item)
        {
            // Overwrite the display item in $parent_boundaries
            $parent_boundaries[$item->id] = $item->boundary_name." ".$item->boundary_type->boundary_type_name;
        }

        // Add "-- Top Level Boundary --"
        $parent_boundaries[0] = "-- Top Level Boundary --";

        // Put "-- Top Level Boundary --" at the top of the list
        ksort($parent_boundaries);

        // Output
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->top_level_boundaries = ORM::factory('boundary')->where('parent_id', 0)->find_all();
        $this->template->content->boundary_types = $boundary_types;
        $this->template->content->parent_boundaries = $parent_boundaries;

        // Locale (Language) Array
        $this->template->content->locale_array = Kohana::config('locale.all_languages');

        // Javascript Header
        $this->template->js = new View('js/boundary_js');

    }

    public function types()
    {
        $this->template->content = new View('admin/boundary_type');

        // setup and initialize form field names
        $form = array(
            'action' => '',
            'boundary_type_id'=> '',
			'parent_id' => '',
            'boundary_type_name' => ''
        );

        // copy the form as errors, so the errors will be stored with keys
        // corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";

        // To hold the boundary type id
        $boundary_type_id = "";

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite
            // $_POST fields with our own things
            $post = Validation::factory(array_merge($_POST,$_FILES));

            //  Add some filters
            $post->pre_filter('trim', TRUE);
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');

            if ($post->action == 'a')       // Add Action
            {
                // Add some rules, the input field, followed by a list of
                // checks, carried out in order
                $post->add_rules('parent_id', 'required', 'numeric');
                $post->add_rules('boundary_type_name','required','length[3,80]');
                
                // Add callback validation functions
                $post->add_callbacks('parent_id', array($this, 'parent_boundary_type_id_check'));
            }
            elseif ($post->action == 'd')
            {
                $post->add_rules('boundary_type_id.*', 'required', 'numeric');
            }

            // Test to see if things passed the rule checks
            if ($post->validate())
            {

                if( $post->action == 'd' ) // Delete Action
                {
                    // Go over all selected boundary types and delete
                    foreach ($post->boundary_type_id as $type_id)
                    {
                        ORM::factory('boundary_type')->delete($type_id);
                    }

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.deleted');

                }
                else if( $post->action == 'a' ) // Save Action
                {
                    $boundary_type = new Boundary_Type_Model($post->boundary_type_id);
                    $boundary_type->boundary_type_name = $post->boundary_type_name;
                    $boundary_type->parent_id = $post->parent_id;
                    $boundary_type->save();

                    $boundary_type_id = $boundary_type->id;

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.added_edited');

                    // Clear the form values
                    array_fill_keys($form, '');
                }
            }
            // No! We have validation errors, we need to show the form
            // again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors,$post->errors('huduma'));

                // Fail!
                $form_error = TRUE;
            }
        }

        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
            'total_items'    => ORM::factory('boundary_type')->count_all()
        ));

        $boundary_types = ORM::factory('boundary_type')
                            ->orderby('id', 'ASC')
                            ->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);


        $parents_array = ORM::factory('boundary_type')
                                 ->where( (!$boundary_type_id)
                                            ? array('id >' => '0')
                                            : array('parent_id !=' => $boundary_type_id))
                                 ->select_list('id', 'boundary_type_name');

        // add none to the list
        $parents_array[0] = "--- Top Level Boundary Type ---";

        // Put "--- Top Level Boundary Type ---" at the top of the list
        ksort($parents_array);

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->boundary_types = $boundary_types;

        $this->template->content->parents_array = $parents_array;

        // Locale (Language) Array
        $this->template->content->locale_array = Kohana::config('locale.all_languages');

        // Javascript Header
        $this->template->js = new View('js/boundary_type_js');
    }

    
//> INPUT VALIDATION CALLBACK METHODS

    /**
     * Checks if the specified parent id exists in the database and ensures the parent id
     * is not the same as the id of the selected boundray
     *
     * @param Validation $post
     */
    public function parent_boundary_id_check(Validation $post)
    {
        // Check if an error for the parent id already exists
        if (array_key_exists('parent_id', $post->errors()))
            return;

        // If the parent id is "0" exit
        if ($post->parent_id == 0)
            return;
        
        // Check for add/edit operation, get the boundary id
        $boundary_id = ($post->boundary_id)? $post->boundary_id : FALSE;

        // Get the specified boundary id
        $parent_id = $post->parent_id;

        // Check if parent and boundary id are the same
        if ($boundary_id AND ($boundary_id == $parent_id))
        {
            $post->add_error('parent_id', 'The boundary and parent cannot be the same');
        }

        // Check if parent exists
        $parent_exists = ORM::factory('boundary', $parent_id)->loaded;

        if ( ! $parent_exists)
        {
            $post->add_error('parent_id', 'Invalid parent boundary');
        }
    }

    /**
     * Checks if the specified boundary type id exists
     * @param Validation $post
     */
    public function boundary_type_id_check(Validation $post)
    {
        // Check if an error for the boundary type id exists
        if (array_key_exists('boundary_type_id', $post->errors()))
            return;

        // Check if the boundary type exists
        $boundary_type_exists = ORM::factory('boundary_type', $post->boundary_type_id)->loaded;

        if ( ! $boundary_type_exists)
        {
            $post->add_error('boundary_type_id', 'Invalid boundary type');
        }
    }

    /**
     * Checks if the parent boundary type exists and ensures the parent boundary type
     * is not the same as the the id of the selected boundary type
     * 
     * @param Validation $post
     */
    public function parent_boundary_type_id_check(Validation $post)
    {
        // Check if an error for the parent id already exists
        if (array_key_exists('parent_id', $post->errors()))
            return;

        // If the parent id is "0" exit
        if ($post->parent_id == 0)
            return;

        // Check for add/edit operation, get the boundary type id
        $boundary_type_id = ($post->boundary_type_id)? $post->boundary_type_id : FALSE;

        // Get the specified boundary id
        $parent_id = $post->parent_id;

        // Check if parent and boundary id are the same
        if ($boundary_type_id AND ($boundary_type_id == $parent_id))
        {
            $post->add_error('parent_id', 'The boundary type and parent cannot be the same');
        }

        // Check if parent exists
        $parent_exists = ORM::factory('boundary_type', $parent_id)->loaded;

        if ( ! $parent_exists)
        {
            $post->add_error('parent_id', 'Invalid parent boundary type');
        }
    }
   
}