<?php
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
		$interval = (int) $total/(count($choropleth_data) * 60);
		
		// Display the data
		$this->template->content->categories = Category_Model::get_dropdown_categories();
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
		
		// Get the county id for the selected feature
		$data = ORM::factory('boundary')->where(array('unique_id' => $feature_id, 'boundary_type' => 1))->find_all();
		$data = $data->as_array();
		if (count($data) == 0 OR count($data) > 1)
		{
			print "No data found";
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
			
			$content_view = new View('huduma/opendata_feature_content');
			$content_view->boundary_name = $data[0]->boundary_name.' '.$data[0]->get_boundary_type_name();
			$content_view->data_title = $metadata_defs->metadata_title;
			$content_view->feature_data = $boundary_data;
			
			// Garbage collection
			unset($result, $columns, $data);
			
			// Return content
			print $content_view;
		}
	}
	
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
}
?>