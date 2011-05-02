<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Entities Controller
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
class Entities_Controller extends Admin_Controller {

    /**
     * Displays the list of static entities for the type specified in @param $type_id
     *
     * @param int $type_id
     */
    public function index($type_id = FALSE)
    {
        // Load the content view for this page
        $this->template->content = new View("admin/entities");

        // Form submission status flags
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";

        // Check, has the form been submitted, if so setup validation
        if ($_POST)
        {
            // Set up validation
            $post = Validation::factory($_POST);

            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field followed by the list of checks, in that order
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            $post->add_rules('static_entity_id.*', 'required', 'numeric');

            // Test is the submission passsed the rule checks
            if ($post->validate())
            {
                if ($post->action == 'd')
                {
                    // Delete each selected static entity
                    foreach ($post->static_entity_id as $item)
                    {
                        ORM::factory('static_entity')->delete($item);
                    }

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
            //> END validation

        }

        // Set up pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
            'total_items' => ($type_id)
                ? Static_Entity_Model::entities($type_id)->count_all()
                : ORM::factory('static_entity')->count_all()
        ));

        // Get the list of entities from the DB
        $entities = ($type_id)
            ? Static_Entity_Model::entities($type_id)->find_all()
            : ORM::factory('static_entity')
                ->find_all((int)Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

        // Set the report variables
        $this->template->content->entities = $entities;
        $this->template->content->pagination = $pagination;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript Header
        $this->template->js = new View("js/entities_js");
    }
    
    /**
     * Displays the list of static entity types
     */
    public function types()
    {
        $this->template->content = new View("admin/entity_types");

        // Setup and initialize form field names
        // Set up and initalize the form fields
        $form = array(
            'entity_type_id' => '',
            'type_name' => '',
            'entity_type_color' => '',
            'category_id' => '',
            'entity_type_image' => '',
            'entity_type_image_thumb' => ''
        );

        // Copy the form as errors so that the errors are stored with the keys corresponding to the field names
        $errors = $form;
        
        // Form validation flags
        $form_saved = FALSE;
        $form_error = FALSE;
        $form_action = "";  // To hold the form action
        
        if ($_POST)
        {
            // Setup validation
            $post = Validation::factory(array_merge($_POST, $_FILES));
            
            // Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by some checks, in that order
            $post->add_rules('action', 'required', 'alpha', 'length[1,1]');
            
            if ($post->action == 'a')
            {
                $post->add_rules('type_name', 'required');
                $post->add_rules('category_id', 'required', array('Category_Model', 'is_valid_category'));
                $post->add_rules('entity_type_image', 'upload::valid', 'upload::type[gif, jpg, png]', 'upload::size[50K]');
            }
            elseif ($post->action == 'd')
            {
                $post->add_rules('entity_type_id.*', 'required', 'numeric');
            }

            // Test if the validation passed
            if ($post->validate())
            {

                if ($post->action == 'd') // Delete static entity
                {
                    // Delete each of the selected entity types
                    foreach ($post->entity_type_id as $item)
                    {
                        // TODO: Check if the entity type has any entities defined under it
                        ORM::factory('static_entity_type')->delete($item);
                    }

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.deleted');
                }
                elseif ($post->action == 'a')
                {
                    $static_entity_type = new Static_Entity_Type_Model($post->entity_type_id);
                    
                    // Set the static entity properties
                    $static_entity_type->type_name = $post->type_name;
                    $static_entity_type->category_id = $post->category_id;
                    $static_entity_type->entity_type_color = $post->entity_type_color;

                    // Save the static entity type
                    $static_entity_type->save();

                    // Upload image/icon
//                    $filename = upload::save('entity_type_image');
                    $filename = FALSE;
                    if ($filename)
                    {
                        // Generate unique filename for the uploaded image
                        $new_filename = "entity_type_".$static_entity_type->id."_".time();

                            // Resize Image to 32px if greater
                        Image::factory($filename)->resize(32, 32, Image::HEIGHT)
                                ->save(Kohana::config('upload.directory', TRUE) . $new_filename . ".png");
                        // Create a 16x16 version too
                        Image::factory($filename)->resize(16, 16, Image::HEIGHT)
                                ->save(Kohana::config('upload.directory', TRUE) . $new_filename . "_16x16.png");

                        // Remove the temporary file
                        unlink($filename);

                            // Delete Old Image
                        $entity_type_old_image = $static_entity_type->entity_type_image;
                        if ( ! empty($category_old_image) AND file_exists(Kohana::config('upload.directory', TRUE) . $entity_type_old_image))
                            unlink(Kohana::config('upload.directory', TRUE) . $entity_type_old_image);

                        $static_entity_type->entity_type_image = $new_filename.".png";

                        // TODO Add thumb image column in table
                        $static_entity_type->entity_type_image_thumb = $new_filename."_16x16.png";

                        // Save
                        $static_entity_type->save();

                    }

                    // Success
                    $form_saved = TRUE;

                    // Set the form action
                    $form_action = Kohana::lang('ui_admin.added_edited');

                    // Empty the $form array
                    array_fill_keys($form, '');
                }
            }
            else // Validation failed
            {                
                // Repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // Overwrite the errors
                $errors = arr::overwrite($errors, $post->errors('static entity'));

                // Turn on the form error
                $form_error = TRUE;
            }
        }

        // Set up pagination
        $pagination = new Pagination(array(
            'query_string' => 'page',
            'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'),
            'total_items' => ORM::factory('static_entity_type')
                                ->count_all()
        ));

        // Get the entity types
        $entity_types = ORM::factory('static_entity_type')
                                ->find_all((int)Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

        // Set the content for the view
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_action = $form_action;
        $this->template->content->pagination = $pagination;

        // Entity types
        $this->template->content->entity_types = $entity_types;

        // Categories
        $this->template->content->categories = ORM::factory('category')
                                                    ->where('parent_id', '0')
                                                    ->select_list('id', 'category_title');

        // Total items
        $this->template->content->total_items = $pagination->total_items;

        // Javascript Header
        $this->template->colorpicker_enabled = TRUE;
        $this->template->js = new View("js/entity_types_js");
    }


	/**
	 * Saves a dashboard role
	 */
	public function save()
	{
		// Load up the view
		$this->template = "";
		$this->auto_render = FALSE;

		// To hold the output json string
		$output_json = "";

		if ($_POST)
		{
			// Manually specify the fields to be validated
			$data = arr::extract($_POST, 'id', 'metadata_item', 'description',
			'static_entity_type_id', 'action');
			$action = $data['action'];
			
			// Instance for validation
			$static_entity_type_metadata = new Static_Entity_Type_Metadata_Model($data['id']);

			// Validation
			if ($static_entity_type_metadata->validate($data))
			{
				// Success! Save
				$static_entity_type_metadata->save();


				// Build output JSON string
				$output_json = json_encode(array(
					'success' => TRUE,
					'message' =>  Kohana::lang('ui_huduma.metadata_item_saved')
				));
				
			}
			else
			{
				// Validation failed
				$output_json = json_encode(array(
					'success' => FALSE,
					'message' => Kohana::lang('ui_main.validation_error').'<div>'.$data->errors().'<div>'
				));
			}
		}

		// Print the JSON string
		header("Content-type: application/json; charset=utf-8");
		print $output_json;
	}




	/**
	 * Loads the contents for the add static entity type metadata dialog
	 * 
	 * @param int $static_entity_type_id
	 */
	public function get($static_entity_type_id = FALSE)
	{
		// Load the view
		$this->template = new View('admin/static_entity_type_dialog');

		// Set up form fields
		$dialog_form = array(
			'id' => '',
			'metadata_item' => '',
			'description' => '',
			'static_entity_type_id' => '',
		);


		$entity_types = Static_Entity_Type_Model::get_entity_types_dropdown();
		$entity_types[0] = "---".Kohana::lang('ui_huduma.select_entity_type')."---";
		ksort($entity_types);

		// Set the content for the view
		$this->template->dialog_form = $dialog_form;
		$this->template->entity_types = $entity_types;

	}




    /**
     * Loads the page for editing/creating a static entity
     *
     * @param int $type_id
     * @param int $entity_id
     */
    public function edit($entity_id = FALSE, $saved = FALSE)
    {
        $this->template->content = new View("admin/entities_edit");

        // Set up and initialize the form fields
        $form = array(
            'static_entity_id' => '',
            'static_entity_type_id' => '',
            'boundary_id' => '',
            'agency_id' => '',
            'entity_name' => '',
            'latitude' => '',
            'longitude' => ''
        );

        // Copy the forms as erros so that the errors are stored with keys corresponding for the field names
        $errors = $form;

        // Form submission status flags
        $form_saved = ($saved == 'saved')? TRUE : FALSE;
        $form_error = FALSE;
        $form_action = "";
		$entity_metadata = "";

        // Load the static entity
        $static_entity = "";

        // Check if the form has been submitted
        if ($_POST)
        {
            // Instance for validation and subsequent saving
            $static_entity = new Static_Entity_Model($entity_id);
            
            // Manually extract the data to be validated
            $data = arr::extract($_POST, 'static_entity_type_id', 'boundary_id', 'entity_name', 'latitude', 'longitude');
            
            // Validate
            if ($static_entity->validate($data))
            {
                // SUCCESS! Save the static entity
                $static_entity->save();

                // Set the entity id
                $entity_id = $static_entity->id;

                // Empty the form array
                array_fill_keys($form, '');

                // Save and close?
                if ($_POST['save'] == 1)
                {
                    url::redirect('admin/entities/edit/'.$static_entity->id.'/saved');
                }
                else
                {
                    url::redirect('admin/entities');
                }
            }
            // Validation failed
            else
            {
                // Repopulate the form fields
                $form = arr::overwrite($form, $data->as_array());

                // Populate the form errors if any
                $errors = arr::overwrite($errors, $data->errors('entity'));

                $form_error = TRUE;
            }
        }
        else
        {
            // Has the entity id been specified, load data
            if ($entity_id)
            {
                $static_entity = ORM::factory('static_entity', $entity_id);

                if ($static_entity->loaded)
                {
                    // Set the form data
                    $form = array(
                        'static_entity_id' => $static_entity->id,
                        'static_entity_type_id' => $static_entity->static_entity_type_id,
                        'boundary_id' => $static_entity->boundary_id,
                        'agency_id' => $static_entity->agency_id,
                        'entity_name' => $static_entity->entity_name,
                        'latitude' => $static_entity->latitude,
                        'longitude' => $static_entity->longitude
                    );
                
					// Check if the static entity has any metadata
					$entity_metadata = new View('admin/entity_metadata_view');
					$entity_metadata->static_entity_id = $entity_id;
					$entity_metadata->metadata_items = Static_Entity_Model::get_metadata($entity_id);
                }
            }
        }

        // Get the entity types
        $entity_types = ORM::factory('static_entity_type')->select_list('id', 'type_name');

        // Get the administatrative boundaries
        $boundaries = ORM::factory('boundary')->select_list('id', 'boundary_name');
        $boundaries[0] = "-- National --";
        ksort($boundaries);

        $agencies = ORM::factory('agency')->select_list('id', 'agency_name');

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_action = $form_action;
        $this->template->content->entity = $static_entity;
        $this->template->content->entity_types = $entity_types;
        $this->template->content->boundaries = $boundaries;
        $this->template->content->agencies = $agencies;
        $this->template->content->static_entity_id = $entity_id;
		$this->template->content->entity_metadata = $entity_metadata;

        // TODO Unpack the metadata on the frontend (view page)

        //Javascript Header
        $this->template->map_enabled = TRUE;
        $this->template->js = new View('js/entity_edit_js');
        $this->template->js->default_map = Kohana::config('settings.default_map');
        $this->template->js->default_zoom = Kohana::config('settings.default_zoom');

        if (!$form['latitude'] || !$form['latitude'])
        {
            $this->template->js->latitude = Kohana::config('settings.default_lat');
            $this->template->js->longitude = Kohana::config('settings.default_lon');
        }
        else
        {
            $this->template->js->latitude = $form['latitude'];
            $this->template->js->longitude = $form['longitude'];
        }
        
        $this->template->js->add_metadata_dialog_url = url::site().'admin/entities/metadata_add/'.$entity_id;
        $this->template->js->metadata_update_url = url::site().'admin/entities/metadata_save';
    }

	/**
	 * Generates the HTML for adding a metadata item on the UI
	 */
	public function metadata_new()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Verify that the entity id exists
		if ($_POST AND Static_Entity_Model::is_valid_static_entity($_POST['entity_id']))
		{
			$item_id  = $_POST['item_id'];

			// Build the HTML for the metadata item
			$html = "<div class=\"row\">";

			// Metadata label
			$html .= "<div class=\"forms_item\">";
			$html .= "<h4>".Kohana::lang('ui_huduma.item_label')."</h4>";
			$html .= "<input type=\"text\" name=\"metadata_label\" id=\"metadata_label_".$item_id."\" class=\"text medium\" value=\"\">";
			$html .="</div>";
			
			// Metadata value
			$html .= "<div class=\"forms_item\">";
			$html .= "<h4>".Kohana::lang('ui_huduma.value')."</h4>";
			$html .= "<input type=\"text\" name=\"metadata_value\" id=\"metadata_value_".$item_id."\" class=\"text\" value=\"\">";
			$html .="</div>";
			
			// Metadata date
			$html .= "<div class=\"forms_item\">";
			$html .= "<h4>".Kohana::lang('ui_huduma.as_of_year')."</h4>";
			$html .= "<input type=\"text\" maxchars=\"4\" name=\"metadata_as_of_date\" id=\"metadata_as_of_year_".$item_id."\" class=\"text\" value=\"".date('Y')."\">";
			$html .="</div>";

			$html .= "</div>";

			print json_encode(array(
				'status' => TRUE,
				'response' => $html
			));
		}
		else
		{
			print json_encode(array(
				'status' => FALSE,
				'response' => Kohana::lang('ui_servicedeliery.invalid_static_entity')
			));
		}
	}

	/**
	 * Saves metadata items in a json format
	 */
	public function metadata_save()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		$entity_id  = $_POST['entity_id'];

		if ($_POST AND Static_Entity_Model::is_valid_static_entity($entity_id))
		{
		    // Set up validation, add some filters and validation rules
		    $post = Validation::factory($_POST)
		                ->pre_filter('trim')
		                ->add_rules('metadata_label.*', 'required')
		                ->add_rules('metadata_value.*', 'required')
		                ->add_rules('metadata_as_of_year.*', 'required', 'numeric');
			
			// Test validation rules
			if ($post->validate() AND count($_POST) > 1)
			{
				// To hold the new metadata
				$new_metadata = array();
				
				// Iterate over $_POST array and create a json structure for each item
				for ($i=0; $i < count($post->metadata_value); $i++)
				{
				    // Create metadata entry
				    $static_entity_metadata = new Static_Entity_Metadata_Model();
				    $static_entity_metadata->static_entity_id = $entity_id;
				    $static_entity_metadata->item_label = $post->metadata_label[$i];
				    $static_entity_metadata->item_value = $post->metadata_value[$i];
				    $static_entity_metadata->as_of_year = $post->metadata_as_of_year[$i];
				    
				    // SAVE
				    $static_entity_metadata->save();
				    
				    // Construct JSON 
					$json_item = "{";
					$json_item .= "\"label\": \"".$post->metadata_label[$i]."\",";
					$json_item .="\"value\": \"".$post->metadata_value[$i]."\",";
					$json_item .= "\"as_of_year\": \"". $post->metadata_as_of_year[$i]."\"";
					$json_item .="}";

					array_push($new_metadata, $json_item);
				}

				print json_encode(array(
					'status' => TRUE,
					'metadata' => json_decode("[".implode(",", $new_metadata)."]")
				));
			}
			else
			{
				print json_encode(array(
					'status' => FALSE,
					'message' => Kohana::lang('ui_huduma.error.invalid_metadata')
				));
			}
		}
		else
		{
			print json_encode(array(
				'status' => FALSE,
				'message' => Kohana::lang('ui_huduma.error.invalid_entity_id')
			));
		}
	}
	
	/**
	 * Loads the view for the metadata dialog
	 */
	public function metadata_add($entity_id)
	{
	    $this->template = new View("admin/entity_metadata_dialog");
	    $this->template->static_entity_id = $entity_id;
	}
    
}
?>
