<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Entities Controller for the frontend
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Entities Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Entities_Controller extends Frontend_Controller {
    var $logged_in;

    public function index()
    {
        // Cacheable controller
        $this->is_cachable = TRUE;

        $this->template->header->this_page = 'entities';
        $this->template->content = new View('frontend/entities');

        // Database instance
//        $db = new Database;

        // Get entity type ids if we're allowed to filter by category
        $entity_type_ids = array();
        if (isset($_GET['e']) AND !empty($_GET['e']) AND $_GET['e'] != 0)
        {
            $static_entity_type_id = (int)$_GET['e'];

            array_push($entity_type_ids, $static_entity_type_id);
        }

        // Check if the category id has been set
        if (isset($_GET['c']) AND !empty($_GET['c']) AND $_GET['c'] != 0)
        {
            $category_id = (int)$_GET['c'];
            
            // Get list of entity types with the category $category_id
            $entity_types = ORM::factory('static_entity_type')->where('category_id', $category_id)->find_all();
            
            // Add entity types to $entity_type_ids
            foreach ($entity_types as $type)
            {
                array_push($entity_type_ids, $type->id);
            }
        }
        
        // To hold the where clauses for the query
        $where_entity_type_id = '1=1';
        if (count($entity_type_ids) > 0)
        {
            $where_entity_type_id = 'static_entity_type_id IN ('.implode(',', $entity_type_ids).')';
        }

        // Break apart location variables
        $southwest = isset($_GET['sw'])? explode(",", $_GET['sw']) : array();
        $northeast = isset($_GET['ne'])? explode(",", $_GET['ne']) : array();

        // To hold the lat/lon where clause
        $where_latlon = '1=1';
        if (count($northeast) == 2 AND count($southwest) == 2)
        {
            // Get the lat/lon values for the bounding box
			$lon_min = (float) $southwest[0];
			$lon_max = (float) $northeast[0];
			$lat_min = (float) $southwest[1];
			$lat_max = (float) $northeast[1];

            $where_latlon = array();

            // Build the where clause based on the bounding box ($ne and $sw values)
            $where_clause = arr::merge($where_latlon, array(
                'latitude >=' => $lat_min,
                'latitude <=' => $lat_max,
                'longitude >=' => $lon_min,
                'longitude <=' => $lon_max
            ));
        }
        
        // Pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page'),
            'total_items' => ORM::factory('static_entity')
                                ->where($where_entity_type_id)
                                ->where($where_latlon)
                                ->count_all()
        ));

        // Entities for the current page
        $entities = ORM::factory('static_entity')
                                ->where($where_entity_type_id)
                                ->where($where_latlon)
                                ->find_all((int) Kohana::config('settings.items_per_page'), $pagination->sql_offset);
        
        // Extract URL variables
        $this->template->content->entities = $entities;
        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;

        $this->template->header->header_block = $this->themes->header_block();
    }

	public function view($entity_id = FALSE, $saved = FALSE)
    {
		// Entity id not specified, redirect to entity listing
        if ( ! $entity_id OR $entity_id == 0)
        {
            url::redirect('frontend/entities');
        }

        $this->template->content = new View("frontend/entity_view");
		// Load Akismet API Key (Spam Blocker)

        $api_akismet = Kohana::config('settings.api_akismet');

        $entity = ORM::factory('static_entity', $entity_id);

        // Set up the form for comments
        $form = array(
            'comment_author' => '',
            'comment_description' => '',
            'comment_email' => '',
            'comment_ip' => '',
            'captcha' => ''
        );


        // Copy the forms as erros so that the errors are stored with keys corresponding for the field names	
        $errors = $form;

        // Form submission status flags
        $captcha = Captcha::factory();
        $form_saved = ($saved == 'saved')? TRUE : FALSE;
        $form_error = FALSE;
        $form_action = "";
		$show_metadata = empty($entity->metadata) ? FALSE : TRUE;

        // Check if the form has been submitted and that the endity ID is valid
        if ($_POST AND Static_Entity_Model::is_valid_static_entity($entity_id))
        {            
            // Manually extract the data to be passed on for validation and subsequent saving
            $data = arr::extract($_POST, 'comment_author', 'comment_email', 'comment_description');
            
            // Add the the static entity id to the data array
            $data = array_merge($data, array('static_entity_id' => $entity_id));
            
            // Validate the captcha
            $valid_captcha = Captcha::valid($_POST['captcha']);
            
            // Static Entity Comment instance
            $entity_comment = new Static_Entity_Comment_Model();

            // Validation check
            if ($entity_comment->validate($data) AND $valid_captcha)
            {
                // To hold the SPAM status of the comment
                $comment_spam = 0;
                
                // Yes! everything is valid
                if ($api_akismet != "")
                {
                    // Run Akismet Spam Checker
                    $akismet = new Akismet();

                    // Comment data
                    $comment = array(
                        'author' => $entity_comment->comment_author,
                        'email' => $entity_comment->comment_email,
                        'website' => "",
                        'body' => $entity_comment->comment_description,
                        'user_ip' => $_SERVER['REMOTE_ADDR']
                    );

                    $config = array(
                        'blog_url' => url::site(),
                        'api_key' => $api_akismet,
                        'comment' => $comment
                    );

                    $akismet->init($config);
						
                    if ($akismet->errors_exist())
                    {
                        if ($akismet->is_error('AKISMET_INVALID_KEY'))
                        {
                            // throw new
                            // Kohana_Exception('akismet.api_key');
                        }
                        elseif ($akismet->is_error('AKISMET_RESPONSE_FAILED'))
                        {
                            // throw new
                            // Kohana_Exception('akismet.server_failed');
                        }
                        elseif($akismet->is_error('AKISMET_SERVER_NOT_FOUND'))
                        {
                            // throw new
                            // Kohana_Exception('akismet.server_not_found');
                        }

                        // If the server is down, we have to post
                        // the comment :(
                        // $this->_post_comment($comment);

                        $comment_spam = 0;
                    }
                    else
                    {
                        $comment_spam = ($akismet->is_spam())? 1:0;
                    }
                }
                else
                {
                    // No API Key!!
                    $comment_spam = 0;
                }

				// Activate comment for now
                if ($comment_spam == 1)
                {
                    $entity_comment->comment_spam = 1;
                    $entity_comment->comment_active = 0;
                }
                else
                {
                    $entity_comment->comment_spam = 0;
                    
                    // Auto Approve
                    // TODO Add configuration comment configuration setting under the static entity
                    // dashboard
                    $entity_comment->comment_active = 1;
                }

                // Save the comment
                $entity_comment->save();
                
                // Success
                $form_saved = TRUE;
                
                array_fill_keys($form, '');
            }
            // Validation failed
            else
            {
                // Repopulate the form fields
                // $form = arr::overwrite($form, $data->as_array());
                
                // Check if the captcha was valid
                // if ( ! $valid_captcha)
                // {
                //     $data->add_error('captcha', Kohana::lang('ui_main.invalid_security_code'));
                // }

                // Populate the form errors if any
                $errors = arr::overwrite($errors, $data->errors('entity'));

                $form_error = TRUE;
            }
        }
        
        // ucfirst() conversion each word in the string
        $entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
        
        $this->template->content->entity_id = $entity->id;
        $this->template->content->entity_name = $entity_name;
        $this->template->content->boundary_id = $entity->boundary_id;
        $this->template->content->latitude = $entity->latitude;
        $this->template->content->longitude = $entity->longitude;		
        $this->template->content->show_dashboard_panel = FALSE;
        $this->template->content->show_metadata = $show_metadata;
        
        // Show the comments
        $entity_view_comments = new View('frontend/entity_view_comments');
        $entity_view_comments->comments = Static_Entity_Model::get_comments($entity_id);
        
        $this->template->content->entity_view_comments = $entity_view_comments;

        // Load the comments form
        $entity_comments_form = new View('frontend/entity_comments_form');
                
        // Set the form content
        $entity_comments_form->captcha = $captcha;
        $entity_comments_form->form = $form;
        $entity_comments_form->errors = $errors;
        $entity_comments_form->form_error = $form_error;
        $entity_comments_form->form_saved = $form_saved;
        
        $this->template->content->entity_comments_form = $entity_comments_form;

        //Javascript Header
        $this->themes->map_enabled = TRUE;
        $this->themes->js = new View('js/entity_view_js');
        $this->themes->js->default_map = Kohana::config('settings.default_map');
        $this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
        $this->themes->js->entity_name = preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($entity->entity_name));
        $this->themes->js->entity_id = $entity_id;

        if ( ! $entity->longitude OR ! $entity->latitude)
        {
            $this->themes->js->latitude = Kohana::config('settings.default_lat');
            $this->themes->js->longitude = Kohana::config('settings.default_lon');
        }
        else
        {
            $this->themes->js->latitude = $entity->latitude;
            $this->themes->js->longitude = $entity->longitude;
        }

        $this->template->header->header_block = $this->themes->header_block();

    }
}
?>
