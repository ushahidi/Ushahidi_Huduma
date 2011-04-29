<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Dashboard home page controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Home page
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Home_Controller extends Dashboard_Template_Controller {

    /**
     * Dashboard landing page
     */
	public function index()
	{
		// Setup the form
		$form = array(
			'comment_description' => ''
		);

		// Copy the form as errors, so the errors maintain the same keys as the field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

        // Load the dashboard panel view
        $dashboard_panel = new View('frontend/dashboards/dashboard_panel');
        $dashboard_panel->static_entity_panel = TRUE;
        
		// Has the static entity role been specified, get content
		if ($this->static_entity_role)
		{
			// Load the static entity view
			$this->template->content = new View('frontend/entity_view');
			
			// TODO Has the form been submitted
			if ($_POST)
			{
			    // Manually extract the data
			    $data = arr::extract($_POST, 'comment_description');
			    
			    $data = array_merge($data, array(
			        'dashboard_user_id' => $this->user->id, 
			        'comment_author' => $this->user->name,
			        'comment_email' => $this->user->email,
			        'static_entity_id' => $this->static_entity_id
			    ));
			    			    
			    // Entity comment instance
			    $entity_comment = new Comment_Model();
			    
			    // Validation
			    if ($entity_comment->validate($data))
			    {
			        // Success
			        // Set extra properties
			        $entity_comment->comment_active = 1;
			        $entity_comment->comment_spam = 0;
			        
			        // Success, save!
			        $entity_comment->save();
			        $form_saved = TRUE;
			        $form_error = FALSE;
			        
			        // Clear the form keys
			        array_fill_keys($form, '');
			    }
			    // Validation failed
			    else
			    {
			        // Repopulate form fields
			        $form = arr::overwrite($form, $data->as_array());
			        
			        // Repopulate error fields
			        $errors = arr::overwrite($errors, $data->errors('comments'));
			        
			        // Validation error
			        $form_error = FALSE;
			        $form_saved = FALSE;
			    }
			}
			// end if
						
			// Load the static entity
			$entity = ORM::factory('static_entity', $this->static_entity_id);

			// ucfirst() type conversion on the entity name
			$entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));

			// Set the entity name
			$this->template->content->entity_name = $entity_name;

			// Disable "view metadata" link
			$this->template->content->show_metadata = FALSE;
			$this->template->content->show_dashboard_panel = TRUE;
			$this->template->content->dashboard_panel = $dashboard_panel;
			
			// Get the comments for the static entity
			$entity_reports_view = new View('frontend/entity_reports_view');
			$entity_reports_view->reports = Static_Entity_Model::get_reports($this->static_entity_id);
			
			$this->template->content->entity_reports_view = $entity_reports_view;

			// Load the comments form
			$entity_comments_form = new View('frontend/entity_comments_form');
			$entity_comments_form->is_dashboard_user = TRUE;
			
			// Set the form content
			$entity_comments_form->form = $form;
			$entity_comments_form->errors = $errors;
			$entity_comments_form->form_error = $form_error;
			$entity_comments_form->form_saved = $form_saved;

			$this->template->content->entity_comments_form = $entity_comments_form;

			// Javascript header
			$this->themes->map_enabled = TRUE;
			$this->themes->js = new View('js/entity_view_js');
			$this->themes->js->default_map = Kohana::config('settings.default_map');
			$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
			$this->themes->js->entity_id = $entity->id;
			$this->themes->js->entity_name = $entity_name;
            $this->themes->js->latitude = $entity->latitude;
            $this->themes->js->longitude = $entity->longitude;

			// Set the header block
			$this->template->header->header_block = $this->themes->header_block();
		}
		elseif ($this->agency_role)
		{
			// Role for specific agency

			// Chack if agency role is location bound, by administrative boundary

		}
		elseif ($this->category_role)
		{
			// Role for specific category

			// Check if categoruy role is location bound
		}
		elseif ($this->boundary_role)
		{
			// Role for specific admin boundary

			// Get content for that boundary
		}
	}
	
	/**
	 * Shows the entity profile page for a static entity
	 */
	public function entity_profile()
	{
	    // Ensure the user has a static entity role
	    if ( ! $this->static_entity_role)
	    {
	        // Go back to home page
	        $this->index();
	    }
	    
	    // Load the entity view page with edit options
	    $this->template->content = new View('frontend/dashboards/entity_profile');
	    
	    // Setup forms
	    $form = array(
	        'entity_id' => '',
	        'entity_name' => '',
	        'latitude' => '',
	        'longitude' => '',
	        'static_entity_type_id' => '',
	        'agency_id' => '',
	        'boundary_id' => ''
	    );
	    
	    // Copy form as errors
	    $errors = $form;
	    
	    $form_error = FALSE;
	    $form_saved = FALSE;
	    
	    // Dashboard panel
	    $dashboard_panel = new View('frontend/dashboards/dashboard_panel');
	    $dashboard_panel->static_entity_panel = TRUE;
	    
	    $this->template->content->dashboard_panel = $dashboard_panel;

	    // Retrieve the entity
	    $entity = new Static_Entity_Model($this->static_entity_id);
	    
	    // Has the form been submitted - For metadata update or otherwise
	    if ($_POST)
	    {
	        $data = arr::extract($_POST, 'entity_name', 'longitude', 'latitude', 'agency_id', 'boundary_id', 'static_entity_type_id');
	        
	        // Validation
	        if ($entity->validate($data))
	        {
	            // Success! Save
	            $entity->save();
	            
                // Turn on form_saved
	            $form_saved = TRUE;
	            $form_error = FALSE;
	            
    	        // Set the form values
        	    $form = array(
        	        'entity_name' => $entity->entity_name,
        	        'latitude' => $entity->latitude,
        	        'longitude' => $entity->longitude,
        	        'static_entity_type_id' => $entity->static_entity_type_id,
        	        'agency_id' => $entity->agency_id,
        	        'boundary_id' => $entity->boundary_id,
        	    );
	        }
	        else
	        {
                // Turn on the form error
	            $form_error = TRUE;
	            $form_saved = FALSE;
	            
                // Populate forms and erros with new values
	            $form = arr::overwrite($form, $data->as_array());
	            $errors = arr::overwrite($errors, $data->errors());
	        }
	    }
	    else
	    {
	        // Set the form values
    	    $form = array(
    	        'entity_name' => $entity->entity_name,
    	        'latitude' => $entity->latitude,
    	        'longitude' => $entity->longitude,
    	        'static_entity_type_id' => $entity->static_entity_type_id,
    	        'agency_id' => $entity->agency_id,
    	        'boundary_id' => $entity->boundary_id,
    	    );
	    }
	    
	    $entity_metadata = new View('frontend/dashboards/entity_metadata_view');
	    $entity_metadata->static_entity_id = $this->static_entity_id;
	    $entity_metadata->metadata_items = Static_Entity_Model::get_metadata($this->static_entity_id);
	    
        // Set data for the content view
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_error = $form_error;
        $this->template->content->entity_types_dropdown = Static_Entity_Type_Model::get_entity_types_dropdown();
        $this->template->content->agencies_dropdown = Agency_Model::get_agencies_dropdown();
        $this->template->content->boundaries_dropdown = Boundary_Model::get_boundaries_dropdown();
        $this->template->content->entity_metadata = $entity_metadata;
        
        //  Javascript header
        $this->themes->map_enabled = TRUE;
        $this->themes->js = new View('js/entity_edit_js');
        $this->themes->js->longitude = $entity->longitude;
        $this->themes->js->latitude = $entity->latitude;
        $this->themes->js->default_map = Kohana::config('settings.default_map');
        $this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
        $this->themes->js->add_metadata_dialog_url = url::site().'dashboards/home/metadata_add/'.$this->static_entity_id;
        $this->themes->js->metadata_update_url = url::site().'dashboards/home/metadata_update';
        $this->themes->js .= new View('js/dashboard_common_js');
        
		// Set the header block
		$this->template->header->header_block = $this->themes->header_block();
	}
	
	/**
	 * Change password page
	 */
	public function change_password()
	{
	    // Load the content view
	    $this->template->content = new View('frontend/dashboards/change_password');
	    
	    // Set up form fields
	    $form = array(
	        'name' => $this->user->name,
	        'username' => $this->user->username,
	        'email' => '',
	        'password' => '',
	    );
	    
	    // Copy forms as errors so that the erros maintain the keys corresponding to the field names
	    $errors = $form;
	    
	    $form_saved = FALSE;
	    $form_error = FALSE;
	    
	    // Has te form been submitted
	    if ($_POST)
	    {
	        // Manually extract the data
	        $data = arr::extract($_POST, 'email', 'password', 'confirm_password', 'name', 'email', 'username');
	        
	        // Add the other properties so that the validate method doesn't throw an error
	        $data = array_merge($data, array('is_active' => $this->user->is_active));
	        
	        if ($this->user->validate($data) AND $_POST['save'] == 1)
	        {
	            // Success
	            $this->user->save();
	            
	            $form_saved = TRUE;
	            $form_error = FALSE;
	            
	            array_fill_keys($form, '');
	        }
	        else
	        {
	            $form = arr::overwrite($form, $data->as_array());
	            $error = arr::overwrite($form, $data->errors());
	            
	            // Turn on form error
	            $form_error = TRUE;
	            
	            $form_saved = FALSE;
	        }
	    }
	    else
	    {
	        // Set the email key in the $forms array
	        $form['email'] = $this->user->email;
	    }
	    
	    $dashboard_panel = new View('frontend/dashboards/dashboard_panel');
	    $dashboard_panel->static_entity_panel = TRUE;
	    
	    // Set content data
	    $this->template->content->dashboard_panel = $dashboard_panel;
	    $this->template->content->form = $form;
	    $this->template->content->errors = $errors;
	    $this->template->content->form_error = $form_error;
	    $this->template->content->form_saved = $form_saved;
	    
	    // Javscript header
	    $this->themes->js = new View('js/dashboard_common_js');
	    
	    // Set the header block
	    $this->template->header->header_block = $this->themes->header_block();
	}
	
	/**
	 * Comment moderation page
	 */
	 public function moderate_comments()
	 {
	     // Load template
	     $this->template->content = new View('frontend/dashboards/moderate_comments');
	     
	     // Get the no. of items per page
	     $items_per_page = (int)Kohana::config('settings.items_per_page_admin');
	     
	     // Setup pagination
	     $pagination = new Pagination(array(
	         'query_string' => 'page',
	         'items_per_page' => $items_per_page,
	         'total_items' => ORM::factory('static_entity_comment')->where('static_entity_id', $this->static_entity_id)->count_all()
	     ));
	     
	     // Fetch comments for current page
	     $comments = ORM::factory('static_entity_comment')
	                        ->where('static_entity_id', $this->static_entity_id)
	                        ->find_all($items_per_page, $pagination->sql_offset);
	     
	     $dashboard_panel = new View('frontend/dashboards/dashboard_panel');
	     $dashboard_panel->static_entity_panel = TRUE;
	     $this->template->content->dashboard_panel = $dashboard_panel;
	     $this->template->content->comments = $comments;
	     $this->template->content->pagination = $pagination;
	     $this->template->content->total_items = $pagination->total_items;
	     $this->themes->js = new View('js/dashboard_common_js');
	     $this->template->header->header_block = $this->themes->header_block();
	 }
	 
	 /**
	  * Updates a static entity comment
	  */
	 public function update_comment()
	 {
	     $this->template = "";
	     $this->auto_render = FALSE;
	     
         // Return value
	     header("Content-type: application/json; charset=utf-8");
	     
	     // Check for form POST
	     if ($_POST)
	     {
	         // Get the comment id
	         $comment_id = $_POST['comment_id'];
	         
	         // Validate comment
	         if (Static_Entity_Comment_Model::is_valid_static_entity_comment($comment_id))
	         {
	             // Get the action
	             $action = $_POST['action'];
	             
	             // Load the entity comment and fetch the comment date
	             $comment = ORM::factory('static_entity_comment', $comment_id);
	             $comment_date = $comment->comment_date;
	             
	             // Check for the action
	             if ($action == 'spam')
	             {
	                 // Mark comment as spam
	                 $comment->comment_spam = 1;
	             }
	             elseif ($action == 'notspam')
	             {
	                 // Mark comment as not spam
	                 $comment->comment_spam = 0;
	             }
	             elseif ($action == 'delete')
	             {
	                 // Mark comment as inactive
	                 $comment->comment_active = 0;
	             }
	             elseif ($action == 'undelete')
	             {
	                 // Mark comment as active
	                 $comment->comment_active  = 1;
	             }
	             
	             // Maintain the original comment date
	             $comment->comment_date = $comment_date;
	             
	             // Save comment
	             $comment->save();
	             
	             // Success!
        	     print json_encode(array(
        	       'success' => TRUE
        	     ));
	         }
	         else
	         {
        	     print json_encode(array(
        	       'success' => FALSE
        	     ));
	         }
	         
	     }
	     else
	     {
    	     print json_encode(array(
    	       'success' => FALSE
    	     ));
	     }
	 }
	 
	 /**
	  * Handles metadata update
	  */
	 public function metadata_update()
	 {
	     // No template for this method
	     $this->template = "";
	     $this->auto_render = FALSE;
	     
	     // Set the headers
	     header("Content-type: application/json; charset=utf-8");
	     
	     if ($_POST)
	     {
	         if ($_POST['action'] == 's')
	         {
	             // Save Action
    	         $data = arr::extract($_POST, 'item_value', 'item_label', 'as_of_year');
    	         $data = array_merge($data, array('static_entity_id' => $this->static_entity_id));
    	         
    	         $static_entity_metadata = new Static_Entity_Metadata_Model($_POST['id']);
	         
    	         if ($static_entity_metadata->validate($data))
    	         {
    	             $static_entity_metadata->save();
    	             print json_encode(array('success' => TRUE));
    	         }
    	         else
    	         {
    	             print json_encode(array('success' => FALSE));
    	         }
    	    }
    	    elseif ($_POST['action'] == 'd')
    	    {
    	        // Delete static entity metadata item
    	        $static_entity_metadata = ORM::factory('static_entity_metadata', $_POST['id']);
    	        if ($static_entity_metadata->loaded)
    	        {
    	            $static_entity_metadata->delete();
    	            
    	            print json_encode(array('success' => TRUE));
    	        }
    	        else
    	        {
    	            print json_encode(array('success' => FALSE));
    	        }
    	    }
    	}
    	else
    	{
    	    print json_encode(array(
    	        'success' => FALSE
    	    ));
    	}
    }
    
    /**
     * Loads the view for the metadata dialog
     */
    public function metadata_add($entity_id)
    {
        $this->template = new View("admin/entity_metadata_dialog");
        $this->template->static_entity_id = $this->static_entity_id;
    }
}
?>
