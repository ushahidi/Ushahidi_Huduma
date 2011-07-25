<?php
/**
 * Opendata controller
 *
 * @author Ushahidi Dev Team - http://ushahidi.com
 * @package Huduma- http://github.com/ushahidi/Ushahidi_Huduma
 * @copyright Ushahidi Inc - http://ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Opendata_Controller extends Frontend_Controller {
	
	public function index()
	{
		// Content view
		$this->template->content = new View('huduma/opendata');
		
		// To hold the county data
		$county_data = array();
		$tmp_breakdown_data = array();
		
		// Get all the counties
		$counties = ORM::factory('boundary')->where('boundary_type', '1')->find_all();
		$county_ids = array();
		foreach ($counties as $county)
		{
			$county_ids[] = $county->id;
			if (! empty($county->unique_id))
			{
				$county_data[$county->id]['unique_id'] = $county->unique_id;
				$tmp_breakdown_data[$county->unique_id]['name'] = $county->boundary_name;
			}
		}
		// Kohana::log('debug', Kohana::debug($county_ids));
		
		// Get the consituency metadata
		$sql = 'SELECT b.id, b.parent_id, bm.data_items FROM '.$this->table_prefix.'boundary b '
			. 'INNER JOIN '.$this->table_prefix.'boundary_metadata_items bm ON (bm.boundary_id = b.id) '
			. 'WHERE b.parent_id IN ('.implode(',', $county_ids).') '
			. 'AND bm.boundary_metadata_id = 1';
		
		// Garbage collection
		unset ($county_ids);
		
		// Execute the Query
		$metadata_items = $this->db->query($sql);
		
		// Get the population totals for each constituency and add them to that of the county
		// The values in this array are stored as [county_name][unique_county_id] => population
		$choropleth_data = array();
		foreach ($metadata_items as $item)
		{
			// Fetch the actual metadata values
			$item_data = json_decode($item->data_items, TRUE);
			
			// Check if the county ID exists in the database
			if (isset($county_data[$item->parent_id]))
			{
				// Fetch the total values for the county
				if (isset($choropleth_data[$county_data[$item->parent_id]['unique_id']]))
				{
					$choropleth_data[$county_data[$item->parent_id]['unique_id']] += (int) $item_data[2];
				}
				else
				{
					$choropleth_data[$county_data[$item->parent_id]['unique_id']] = (int) $item_data[2];
				}
			}
		}
		
		// Sort the choropleth data by the frequency
		array_multisort($choropleth_data, SORT_DESC);
		
		
		// Get the color gradient
		$color_gradient = navigator::get_color_gradient_map("072a70", "FFFFFF", count($choropleth_data));
		$i = 0;
		$breakdown_data = array();
		$total = 0;
		foreach ($choropleth_data as $key => $value)
		{
			$choropleth_data[$key] = $color_gradient[$i];
			$breakdown_data[$tmp_breakdown_data[$key]['name']] = array('color' => $color_gradient[$i], 'total' => $value);
			$total += $value;
			$i++;
		}
		
		
		// Garbage collection
		unset ($i, $color_gradient, $metadata_items, $tmp_breakdown_data, $county_data);
		
		// Calculate the interval for the chart

        $chloropleth_divisor = (count($choropleth_data) * 60);

        if($chloropleth_divisor == 0)
            $chloropleth_divisor = 1;

		$interval = (int) $total/$chloropleth_divisor;
		
		// Display the data
		$categories = Category_Model::get_dropdown_categories();
		$categories[0] = "---".Kohana::lang('ui_huduma.select_category')."---";
		ksort($categories);
		
		$this->template->content->basemap_title = Kohana::lang('opendata.basemap').": ".Kohana::lang('opendata.basemap_options.1');
		$this->template->content->categories = $categories;
		$this->template->content->total  =$total;
		$this->template->content->breakdown_data = $breakdown_data;
		
		// Javascript header
		$this->themes->map_enabled = TRUE;
		$this->themes->js = new View('js/opendata_js');
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->latitude = Kohana::config('settings.default_lat');
		$this->themes->js->longitude = Kohana::config('settings.default_lon');
		$this->themes->js->color_map = json_encode($choropleth_data);
		$this->themes->js->interval = $interval;
		
		$this->template->header->header_block = $this->themes->header_block();
	}
	
	public function map_data($feature_id)
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		// Set the content type
		header("Content-type: application/json; charst=utf-8");
		
		// Get the county id for the selected feature
		$data = ORM::factory('boundary')->where(array('unique_id' => $feature_id, 'boundary_type' => 1))->find_all();
		$data = $data->as_array();
		if (count($data) == 0 OR count($data) > 1)
		{
			print json_encode(array("success" => FALSE));
			exit(1);
		}
		else
		{
			// Get the population columns
			$metadata_defs = ORM::factory('boundary_metadata', 1);
			$columns = json_decode($metadata_defs->metadata_columns);
			
			// Get all the data
			$sql = 'SELECT b.id, bm.data_items FROM '.$this->table_prefix.'boundary b '
					. 'INNER JOIN '.$this->table_prefix.'boundary_metadata_items bm ON (bm.boundary_id = b.id) '
					. 'WHERE b.parent_id = '.$data[0]->id;
			
			// To store the boundary data
			$boundary_data = array();
					
			// Execute query
			$result = $this->db->query($sql);
			$boundary_data['No. of constituencies'] = $result->count();
			
			foreach ($result as $item)
			{
				$data_items = json_decode($item->data_items, TRUE);
				
				foreach ($data_items as $key => $value)
				{
					if ( ! isset($boundary_data[$columns[$key]]))
					{
						$boundary_data[$columns[$key]] = 0;
					}
					
					$boundary_data[$columns[$key]] += (int)$value;
					
				}
			}
			
			// Build the boundary name
			$type_name = ($data[0]->boundary_type == 1) ? Kohana::lang('ui_huduma.county') : Kohana::lang('ui_huduma.constituency');
			$boundary_name = $data[0]->boundary_name.' '.$type_name;
			
			// HTML string to return via JSON
			$html_str = "<div class=\"opendata-analytics\">"
						. "<h4>".$metadata_defs->metadata_title.": ".$boundary_name."</h4>"
						. "<table class=\"breakdown_table\">";
			
			foreach ($boundary_data as $key => $value)
			{
				$html_str .= "<tr>"
						. "<td class=\"header\">".$key."</td>"
						. "<td>".$value."</td>"
						. "</tr>";
			}
			
			$html_str .= "</table></div>";
			
			// Garbage collection
			unset($result, $columns, $data);
			
			// Return content
			print json_encode(array(
				"success" => TRUE, 
				"content" => $html_str,
				"piechartData" => array("Female" => $boundary_data["Female"], "Male" => $boundary_data["Male"])
			));
		}
	}

    /**
     * Gets the facilities for the specified category and echoes a HTML response
     * which is then rendered via AJAX on the client side
     * 
     * @param int $category_id Database id the category to be used for fetching the data
     * @return void
     */
	public function get_facilities($category_id)
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		if (Category_Model::is_valid_category($category_id))
		{
			$facility_types = Static_Entity_Type_Model::get_entity_types_dropdown($category_id);
			$facility_types[0] = "---All--";
			ksort($facility_types);
			// Build the return HTML str
			$html_str = "";
			foreach ($facility_types as $key => $value)
			{
				$html_str .= sprintf("<option value=\"%s\">%s</option>", $key, $value);
			}
			
			print $html_str;
		}
	}
	
	/**
	 * Gets the data points to be used for generating a heatmap and echoes
	 * a JSON string
	 */
	public function get_heatmap_data()
	{
		$this->template = '';
		$this->auto_render = FALSE;
		
		header("Content-type: application/json; charset=utf-8");
		
		// Get the parameters
		$category_id = $_GET['category'];
		$facility_type = $_GET['facility_type'];
		
		// Fetch the entities
		$entities = (Static_Entity_Type_Model::is_valid_static_entity_type($facility_type))
				? ORM::factory('static_entity')->where('static_entity_type_id', $facility_type)->find_all()
				: Static_Entity_Model::get_entities_by_category($category_id);
				
		$points = array();
		foreach ($entities as $entity)
		{
			$points[] = array('id' => $entity->id, 'latitude' => $entity->latitude, 'longitude' => $entity->longitude);
		}
		
		print json_encode(array(
			'success' => TRUE,
			'points' => $points
		));
	}
}
?>