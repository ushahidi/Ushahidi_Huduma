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
	
	/**
	 * Generates and returns the view for displaying reports (entity + dashboard reports)
	 * 
	 * @param array $reports List of reports to be displayed in the view
	 * @param string $controller Relative path of the controller to be used to view individual reports
	 */
	public static function get_reports_view($reports, $controller, $pagination)
	{
		// Get the parent view
		$reports_view = View::factory('frontend/entity_reports_view');
		
		$reports_view->reports = $reports;
		$reports_view->pagination = $pagination;
		$reports_view->report_view_controller = $controller;
		
		// Return
		return trim($reports_view);
	}
	
	/**
	 * @return Result
	 */
	public static function get_category_stats($category_id = FALSE)
	{
		// Database instance for the fetch
		$db = new Database();
		
		$sql = 'SELECT c.id, c.category_title, c.category_color, COUNT(i.id) AS total_reports, '
			. '(SELECT COUNT(it.id) FROM incident_ticket it INNER JOIN incident_category ic2 ON (ic2.incident_id = it.incident_id)'
			. ' WHERE ic2.category_id = c.id AND it.report_status_id = 1) AS unresolved, '
			. '(SELECT COUNT(it.id) FROM incident_ticket it INNER JOIN incident_category ic2 ON (ic2.incident_id = it.incident_id)'
			. ' WHERE ic2.category_id = c.id AND it.report_status_id = 2) AS resolved '
			. 'FROM category c '
			. 'INNER JOIN incident_category ic ON (ic.category_id = c.id) '
			. 'INNER JOIN incident i on (ic.incident_id = i.id) '
			. 'WHERE i.incident_active = 1 '
			. 'AND c.category_visible = 1 ';
			
		$sql .= (Category_Model::is_valid_category($category_id))? 'AND c.id = '.$category_id.' ' : '';
		$sql .= 'GROUP BY c.id';
		
		// Return
		return $db->query($sql);
	}
	
	/**
	 * Generates a linear color progression between two colors. Unless specified, the end color
	 * shall default to white (#FFFFFF). The progression/gradient is generated using linear
	 * interpolation
	 *
	 * Credits to: http://www.herethere.net/~samson/php/color_gradient/
	 *
	 * @param string $start_color First color in the gradient map
	 * @param string end_color Last color in the gradient map
	 * @param int $steps No. of colors to generate
	 * @return array
	 */
	public static function get_color_gradient_map($start_color, $end_color = "FFFFFF", $steps = 16)
	{
		// To hold the return value
		$gradient_map = array();
		
		// Convert the start and end colors to HEX
		$start_color = hexdec($start_color);
		$end_color = hexdec($end_color);
		
		// Extract the RGB components for the start and end colors
		$start_red = ($start_color & 0xff0000) >> 16;
		$start_green = ($start_color & 0x00ff00) >> 8;
		$start_blue = ($start_color & 0x0000ff) >> 0;
		
		$end_red = ($end_color & 0xff0000) >> 16;
		$end_green = ($end_color & 0x00ff00) >> 8;
		$end_blue = ($end_color & 0x0000ff) >> 0;
		
		// Build the gradient map
		for ($i = 0; $i < $steps; $i++)
		{
			$red = self::interpolate($start_red, $end_red, $i, $steps);
			$green = self::interpolate($start_green, $end_green, $i, $steps);
			$blue = self::interpolate($start_blue, $end_blue, $i, $steps);
			
			// Generate final color and add it to the map
			$color_item = ((($red << 8) | $green) << 8) | $blue;
			$gradient_map[] = sprintf("%06X", $color_item);
		}
		return $gradient_map;
	}
	
	/**
	 * Gets the interpolated value between p_begin and p_end
	 * @return int
	 */
	public static function interpolate($p_begin, $p_end, $p_step, $p_max)
	{
		return ($p_begin < $p_end)
			? (($p_end  - $p_begin) * ($p_step / $p_max)) + $p_begin
			: (($p_begin - $p_end) * (1- ($p_step / $p_max))) + $p_end;
	}
	
	/**
	 * Gets the age of an incident
	 *
	 * @param string $incident_date Date when the incident was reported
	 * @return string
	 */
	public static function get_incident_age($incident_date)
	{
		// Convert the incident date to string
		$date_string = strtotime($incident_date);
		
		// Calculate difference between years
		$years = date('Y') - date('Y', $date_string);
		$months = ($years == 0)
			? date('n') - date('n', $date_string)
			: date('n') + (12 - date('n', $date_string));
		
		// Check if the difference in months is more than a year
		if ($months >= 12 )
		{
			$years++;
			$months -= 12;
		}
		
		// Compute difference in days
		$days = ($months == 0 AND $years == 0)? date('j') - date('j', $date_string) : 0;
		
		// Time difference computation
		$hours = ($days == 0 AND $months == 0 AND $years == 0)? date('G') - date('G', $date_string): 0;
		$minutes = ($days == 0)? date('i') - date('i', $date_string) : 0;
		$seconds = ($minutes == 9)? date('s') - date('s', $date_string) : 0;
		
		// Log
		Kohana::log('info', sprintf('Incident age is: %d Years, %d Months, %d, Days, %d Hours, %d Minutes, %d Seconds', 
			$years, $months, $days, $hours, $minutes, $seconds));
			
		// Return
		if ($years > 0)
		{
			return sprintf('%d year%s ago', $years, (($years > 1)? 's' : ''));
		}
		elseif ($months > 0)
		{
			return sprintf('%d month%s ago', $months, (($months > 1)? 's' : ''));
		}
		elseif ($days > 0)
		{
			return sprintf('%d day%s ago', $days, (($days > 1)? 's' : ''));
		}
		elseif ($hours > 0)
		{
			return sprintf('%d hour%s ago', $hours, (($hours > 1)? 's' : ''));
		}
		elseif ($minutes > 0)
		{
			return sprintf('%d minute%s ago', $minutes, (($minutes > 1)? 's' : ''));
		}
		elseif ($seconds > 0)
		{
			return sprintf('%d second%s ago', $seconds, (($seconds > 1)? 's' : ''));
		}
	}
}
?>
