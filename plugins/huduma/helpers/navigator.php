<?php defined('SYSPATH') OR die('No direct access allowed.');
/* 
 * Miscellanious helper functions for the huduma plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     navigator helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class navigator_Core {

	/**
	 * Generates the subtabs for the service delivery plugin
	 *
	 * @param String $this_page
	 */
	public static function subtabs($this_sub_page = FALSE)
	{
		// To hold the menus for service delivery
		$menu = "";

		// Administrative boundaries
		$menu .= ($this_sub_page == "boundaries")
			? Kohana::lang('ui_huduma.boundaries')
			: "<a href=\"".url::site()."admin/servicedelivery\">".Kohana::lang('ui_huduma.boundaries')."</a>";

		// Service Providers
		$menu .= ($this_sub_page == "agencies")
			? Kohana::lang('ui_huduma.agencies')
			: "<a href=\"".url::site()."admin/agencies\">".Kohana::lang('ui_huduma.agencies')."</a>";


		// Static Entities
		$menu .= ($this_sub_page == "entities")
			? Kohana::lang("ui_huduma.entities")
			: "<a href=\"".url::site()."admin/entities\">".Kohana::lang('ui_huduma.entities')."</a>";

		// Dashboard users
		$menu .= ($this_sub_page == "dashboard_users")
			? Kohana::lang("ui_huduma.dashboard_users")
			: "<a href=\"".url::site()."admin/dashboard/users\">".Kohana::lang('ui_huduma.dashboard_users')."</a>";
		
		// Output the menu
		echo $menu;
	}

    /**
     * Generates the breadcrumb for the boundary specified in @param $boundary_id
     *
     * @param int $boundary_id
     */
    public static function boundary_breadcrumb($boundary_id)
    {
        // Get the boundary name and parent id
        $boundary = ORM::factory('boundary', $boundary_id);

        // Get the boundary
        $breadcrumb = "";

        // Get the boundary type name
        $boundary_type_name = $boundary->boundary_type->boundary_type_name;
        
        if ($boundary->parent_id > 0)
        {
            $breadcrumb = $boundary->boundary_name." ".$boundary_type_name;
            $breadcrumb = self::boundary_breadcrumb($boundary->parent_id)."&rarr;".$breadcrumb;
        }
        else
        {
            $breadcrumb = $boundary->boundary_name." ".$boundary_type_name.$breadcrumb;
        }
        return $breadcrumb;
    }

    public static function child_boundaries($boundary_id, $recursive = FALSE)
    {
        // To hold the hierarchy
        $hierarchy = '';

        // Get the child boundaries
        $children  = ORM::factory('boundary')->where('parent_id', $boundary_id)->find_all();
        if (count($children) > 0)
        {
            $hierarchy .= "<ul>";
            foreach ($children as $child)
            {
                $parent_css_id = 'boundary_'.$child->id;
                $hierarchy .= "<li>";
                $hierarchy .= "<a href=\"javascript:showHierarchy(".$child->id.", '".$parent_css_id."')\">".$child->boundary_name." ".$child->boundary_type->boundary_type_name."</a>";
                $hierarchy .= "<div id=\"".$parent_css_id."\" style=\"visibility:hidden;\"></div>";
                
                // Check if the boundaries are to be recursively fetched
                if ($recursive)
                {
                    $hierarchy .= self::child_boundaries($child->id);
                }

                $hierarchy .= "</li>";
            }
            $hierarchy .= "</ul>";
        }
        return $hierarchy;
    }
    
    /**
     * Helper function to recursively fetch and build hierarchical HTML for the inline comments 
     * associated with the comment specified in @param $comment_id.
     * 
     * @param   int $comment_id
     * @return  string
     */
	public static function inline_comments($incident_id)
	{
		// To hold the comment tree
		$comment_tree = '';

		// Fetch the inline commnets
		$children = ORM::factory('incident',$incident_id)->comment;

		Kohana::log('info', sprintf('Fetch %d comments for incident %d', $children->count(), $incident_id));
        
		if ($children->count() > 0)
		{
			$comment_tree .= "<ul class=\"children inlinecomments\">";
            
			foreach ($children as $comment)
			{
				$comment_tree .= "<li class=\"inlinecomment\" style=\"clear: both;\"  id=\"dashboard_comment_".$comment->id."\">";
				$comment_tree .= "  <div class=\"dashboard_comment_block\">";

				// Comment author, date and time
				$comment_tree .= "      <div>";
				$comment_tree .= "          <strong>".$comment->comment_author."</strong>&nbsp;";
				$comment_tree .= "          <span class=\"comment_date_time\">";
				$comment_tree .= "          ".date('g:m a', strtotime($comment->comment_date))."&nbsp;on&nbsp;";
				$comment_tree .= "          ".date('F j, Y', strtotime($comment->comment_date));
				$comment_tree .= "          </span>";
				$comment_tree .= "      </div>";

				// Comment description
				$comment_tree .= "      <div class=\"dashboard_comment_text\">";
				$comment_tree .= "          <p>".$comment->comment_description."</p>";
				$comment_tree .= "      </div>";

				$comment_tree .= "  </div>";

				// Comment box/form holder
				$comment_tree .= "<div class=\"comment_box_holder\"></div>";

				// Close the parent comment
				$comment_tree .= "</li>";
			}
			
			$comment_tree .= "</ul>";
		}

		return $comment_tree;
	}
	
	/**
	 * Generates the HTML for adding a new metadata item
	 */
	public static function get_metadata_item_row()
	{
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

			return json_encode(array(
				'status' => TRUE,
				'response' => $html
			));
		}
		else
		{
			return json_encode(array(
				'status' => FALSE,
				'response' => Kohana::lang('ui_huduma.invalid_static_entity')
			));
		}		
	}
	
	/**
	 * Helper function for saving metadata items
	 */
	public static function save_new_metadata_items()
	{
		// Has the form been submitted
		if ($_POST)
		{
			// Get the entity id
			$entity_id  = $_POST['entity_id'];

			if(Static_Entity_Model::is_valid_static_entity($entity_id))
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
						'metadata' => json_decode("[".implode(",", $new_metadata)."]"),
						'message' => Kohana::lang('ui_huduma.metadata_item_added')
					));
				}
				
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
}
?>
