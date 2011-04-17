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
			        // 'parent_comment_id' => $_POST['dashboard_comment_reply_to']
			        'static_entity_id' => $this->static_entity_id));
			    			    
			    // Entity comment instance
			    $entity_comment = new Static_Entity_Comment_Model();
			    
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
			        //$form = array_fill_keys($form, '');
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
						
			// Load the static entity
			$entity = ORM::factory('static_entity', $this->static_entity_id);

			// ucfirst() type conversion on the entity name
			$entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));

			// Set the entity name
			$this->template->content->entity_name = $entity_name;

			// Disable "view metadata" link
			$this->template->content->show_metadata = FALSE;
			$this->template->content->show_dashboard_panel = TRUE;
			
			// Get the comments for the static entity
			$entity_view_comments = new View('frontend/entity_view_comments');
			$entity_view_comments->comments = Static_Entity_Model::get_comments($this->static_entity_id);
			
			$this->template->content->entity_view_comments = $entity_view_comments;

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
}
?>
