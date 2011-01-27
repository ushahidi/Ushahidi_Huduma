<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage huduma methods
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General
 * Public License (LGPL)
 */

class Huduma_Controller extends Admin_Controller
{

    function __construct()
    {   
        parent::__construct();
        $this->template->this_page = 'huduma';

    }


    function index()
    {
        $this->template->content = new View('huduma/boundary');
        $this->template->content->title = 'Administrative Boundaries';

        // setup and initialize form field names
        $form = array
        (
            'action' => '',
            'boundary_id'      => '',
            'boundary_type_id'      => '',
            'boundary_name'      => '',
            'boundary_type_name'      => ''
            );

            // copy the form as errors, so the errors will be stored with keys
            // corresponding to the form field names
            $errors = $form;
            $form_error = FALSE;
            $form_saved = FALSE;
            $form_action = "";
			$boundary_array = array();
            // check, has the form been submitted, if so, setup validation
            if ($_POST)
            {
                // Instantiate Validation, use $post, so we don't overwrite
                // $_POST fields with our own things
                $post = Validation::factory(array_merge($_POST,$_FILES));

                //  Add some filters
                $post->pre_filter('trim', TRUE);

                if ($post->action == 'a')       // Add Action
                {
                    // Add some rules, the input field, followed by a list of
                    // checks, carried out in order
                    $post->add_rules('boundary_name','required','length[3,80]');
					$post->add_rules('boundary_type_id','required');
                }

                // Test to see if things passed the rule checks
                if ($post->validate())
                {
                    $boundary_id = $post->boundary_id;
                    $boundary = new Boundary_Model($boundary_id);

                    if( $post->action == 'd' )
                    { // Delete Action
                        $boundary->delete( $boundary_id );
                        $form_saved = TRUE;
                        $form_action = 'DELETED';

                    }
                    else if( $post->action == 'a' )
                    { // Save Action

                        // Existing Boundary??
                        if ($boundary->loaded==true)
                        {   
							$boundary->boundary_name = $post->boundary_name;
							$boundary->boundary_type_id = $post->boundary_type_id;
							$boundary->save();

                            $form_saved = TRUE;
                            $form_action = "Edited";
                        }   
                        else
                        {   
   							$boundary->boundary_name = $post->boundary_name;
							$boundary->boundary_type_id = $post->boundary_type_id;
							$boundary->save();

                            $form_saved = TRUE;
                            $form_action = "Added";
                        }   
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
                    $form_error = TRUE;
                }
            }

            // Pagination
            $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
                            'total_items'    => ORM::factory('boundary')->count_all()
            ));

            $boundaries = ORM::factory('boundary')
            ->orderby('id', 'ASC')
            ->find_all((int) Kohana::config('settings.items_per_page_admin'),
            $pagination->sql_offset);

            /* Get the list of boundary types */
            $boundary_array = ORM::factory('boundary_type')
            ->select_list('id', 'boundary_type_name');
			
			$this->template->content->form = $form;
            $this->template->content->errors = $errors;
            $this->template->content->form_error = $form_error;
            $this->template->content->form_saved = $form_saved;
            $this->template->content->form_action = $form_action;
            $this->template->content->pagination = $pagination;
            $this->template->content->total_items = $pagination->total_items;
            $this->template->content->boundaries = $boundaries;
			$this->template->content->boundary_array = $boundary_array;

            // Locale (Language) Array
            $this->template->content->locale_array = Kohana::config('locale.all_languages');

            // Javascript Header
            $this->template->colorpicker_enabled = TRUE;
            $this->template->js = new View('js/boundary_js');



    }





    function boundary_type()
    {
        $this->template->content = new View('huduma/boundary_type');
        $this->template->content->title = 'Boundary Types';

        // setup and initialize form field names
        $form = array
        (
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
            // check, has the form been submitted, if so, setup validation
            if ($_POST)
            {
                // Instantiate Validation, use $post, so we don't overwrite
                // $_POST fields with our own things
                $post = Validation::factory(array_merge($_POST,$_FILES));

                //  Add some filters
                $post->pre_filter('trim', TRUE);

                if ($post->action == 'a')       // Add Action
                {
                    // Add some rules, the input field, followed by a list of
                    // checks, carried out in order
					$post->add_rules('parent_id','required','numeric');
                    $post->add_rules('boundary_type_name','required','length[3,80]');
                }

                // Test to see if things passed the rule checks
                if ($post->validate())
                {
                    $boundary_type_id = $post->boundary_type_id;
                    $boundary_type = new Boundary_Type_Model($boundary_type_id);

                    if( $post->action == 'd' )
                    { // Delete Action
                        $boundary_type->delete($boundary_type_id);
                        $form_saved = TRUE;
                        $form_action = 'DELETED';

                    }
                    else if( $post->action == 'a' )
                    { // Save Action

						$boundary_type = ORM::factory('boundary_type',$post->boundary_type_id);

						if($boundary_type->loaded==true)
						{
							$boundary_type->boundary_type_name = $post->boundary_type_name;
							$boundary_type->parent_id = $post->parent_id;
							$boundary_type->save();

							$form_saved = TRUE;
							$form_action = 'EDITED';
						}
						else
						{
							$boundary_type->boundary_type_name = $post->boundary_type_name;
							$boundary_type->parent_id = $post->parent_id;
							$boundary_type->save();

							$form_saved = TRUE;
							$form_action = 'ADDED';

						}
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
            ->find_all((int) Kohana::config('settings.items_per_page_admin'),
            $pagination->sql_offset);


			$parents_array = ORM::factory('boundary_type')
                                     ->where('parent_id','0')
                                     ->select_list('id', 'boundary_type_name');

			// add none to the list
			$parents_array[0] = "--- Top Level Boundary Type ---";

			// Put "--- Top Level Boundary Type ---" at the top of the list
			ksort($parents_array);



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
            $this->template->colorpicker_enabled = TRUE;
            $this->template->js = new View('js/boundary_type_js');



    }


   
}
