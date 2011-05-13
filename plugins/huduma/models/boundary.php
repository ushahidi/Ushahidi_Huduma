<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for the boundaries table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Boundary Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Boundary_Model extends ORM {

	// Table name
	protected $table_name = 'boundary';

	// Relationships
	protected $has_many = array('static_entity', 'incident');

	/**
	 * Validates and optionally saves a new boundary record from an array
	 * 
	 * @param 	array 	$array 	values to check
	 * @param	boolean	$save	Save the record when validation succeeds
	 * @return	boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
					->pre_filter('trim', TRUE)
					->add_rules('boundary_name', 'required')
					->add_rules('boundary_type', 'required', 'chars[1,2]')
					->add_rules('boundary_color', 'required', 'length[6,6]');
		
		// Check if the parent id has been set
		if ( ! empty($array->parent_id) AND $array->parent_id != 0)
		{
			$array->add_rules('parent_id', array('Boundary_Model', 'is_valid_boundary'));
		}
		
		// Pass on validation to the parent
		return parent::validate($array, $save);
	}
	
	/**
	 * Gets the type name of the current boundary
	 *
	 * @return string
	 */
	public function get_boundary_type_name()
	{
		// 
		switch ($this->boundary_type)
		{
			case 1:
			return Kohana::lang('ui_huduma.county');
			
			case 2:
			return Kohana::lang('ui_huduma.constituency');
		}
	}

	/**
	 * Gets the child boundaries for a specific parent boundary
	 *
	 * @param int $parent_id
	 * @return array
 	 */
	public static function get_child_boundaries($parent_id)
	{
		if ( ! self::is_valid_boundary($parent_id) OR $parent_id == 0)
		{
			// Invalid parent, FAIL!
			return FALSE;
		}
		else
		{
			// Return list of boundaries
			return self::factory('boundary')
					->where(array('parent_id' => $parent_id))
					->select_list('id',	'boundary_name');
		}
	}
	
	/**
	 * Gets the list of parent boundaries - the ones that have children
	 *
	 * @return array
	 */
	public static function get_parent_boundaries()
	{
		// Get list
		$parents = self::factory('boundary')
					->select('id', 'boundary_name', 'boundary_type')
					->where('parent_id', 0)
					->orderby('id', 'asc')
					->find_all();
		
		// To hold the return values		
		$list = array();
		foreach($parents as $parent)
		{
			// Construct the boundary name
			$boundary_name = $parent->boundary_name;
			$boundary_name .= ($parent->boundary_type == 1)
								? " ".Kohana::lang('ui_huduma.county') 
								: " ".Kohana::lang('ui_huduma.constituency');
			
			$list[$parent->id] = $boundary_name;
		}
		
		// Return
		return $list;
	}

	/**
	 * Checks if a boundary with the specifid id exists in the database
	 *
	 * @param 	int $boundary_id	Boundary id to lookup in the database
	 * @return 	boolean
	 */
	public static function is_valid_boundary($boundary_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $boundary_id) > 0)
			? self::factory('boundary', $boundary_id)->loaded
			: FALSE;
	}
	
	/**
	 * Gets the list of boundaries for display in a dropdown list
	 *
	 * @return  array
	 */
	public static function get_boundaries_dropdown($show_boundary_type = TRUE)
	{
		if ($show_boundary_type)	// Add the boundary type name to the name of the boundary
		{
			// To hold the return array
			$list = array();
			
			// Fetch all the boundaries
			$boundaries = self::factory('boundary')->select('id', 'boundary_name', 'boundary_type')->find_all();
			
			// Build an array containing the boundary + type name
			foreach ($boundaries as $boundary)
			{
				// Append the boundary type name to the boundary name
				$boundary_name = $boundary->boundary_name;
				$boundary_name .= ($boundary->boundary_type == 1)
									? " ".Kohana::lang('ui_huduma.county') 
									: " ".Kohana::lang('ui_huduma.constituency');

				$list[$boundary->id] = $boundary_name;
			}
			
			return $list;
		}
		else
		{
		    return self::factory('boundary')->select_list('id', 'boundary_name');
		}
	}
	
	/**
	 * Gets the entities within a specific boundary
	 *
	 * @param int $boundary_id Database ID of the boundary in which the entities lie
	 * @return ORM_Iterator
	 */
	public static function get_entities($boundary_id)
	{
		if ( ! self::is_valid_boundary($boundary_id))
		{
			return FALSE;
		}
		else
		{
			// To hold the list of boundaries
			$boundaries = array($boundary_id);
			
			// Get the child boundaries
			$db = new Database();
			$children = $db->from('boundary')->select('id')->where('parent_id', $boundary_id)->get();
			foreach ($children as $child)
			{
				array_push($boundaries, $child->id);
			}
			
			// Return
			return self::factory('static_entity')
						->in('boundary_id', $boundaries)
						->find_all();
		}
	}
	/**
	 * Gets the list of incident reports for a specific boundary including
	 * those for static entities within that boundary
	 *
	 * @param int $boundary_id Database id of the boundary
	 * @param string $status Status filter for the reports to be fetched
	 * @return array
	 */
	public static function get_boundary_reports($boundary_id, $status = 'all')
	{
		if ( ! Boundary_Model::is_valid_boundary($boundary_id))
		{
			return FALSE;
		}
		else
		{
			// Execute the queries
			$db = new Database();
			$table_prefix = Kohana::config('database.table_prefix');
			
			// Base query for fetching the incidents
			$sql = 'SELECT i.id, i.incident_title, i.incident_description, i.incident_date, ';
			$sql .= 'i.incident_mode, COUNT(co.id) AS comment_count,  it.report_status_id AS report_status ';
			$sql .= 'FROM '.$table_prefix.'incident i ';
			$sql .= 'INNER JOIN '.$table_prefix.'incident_category ic ON (ic.incident_id = i.id) ';
			$sql .= 'INNER JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) ';
			$sql .= 'LEFT JOIN '.$table_prefix.'comment co ON (co.incident_id = i.id) ';
			// Add join depending on the value of @param $status
			$sql .= (strtolower($status) == 'all')? 'LEFT JOIN incident_ticket it ON (it.incident_id = i.id) ': '';
			$sql .- (strtolower($status) == 'resolved' OR $status = 'unresolved')
					? 'INNER JOIN '.$table_prefix.'incident_ticket it ON (it.incident_id = i.id) ' 
					: '';
			
			$sql .= 'WHERE c.category_visible = 1 ';
			$sql .= 'AND i.incident_active = 1 ';
			$sql .= 'AND i.boundary_id = %d ';
			$sql .- (strtolower($status) == 'resolved')? 'AND it.report_status_id = 2 ' : '';
			$sql .- (strtolower($status) == 'unresolved')? 'AND it.report_status_id = 1 ' : '';
			
			// Apply string formatting
			$sql = sprintf($sql, $boundary_id);
			
			// Get the static entities for the boundary in @param $boundary_id
			$entities_query = 'SELECT e.id FROM '.$table_prefix.'static_entity e '
							. 'INNER JOIN '.$table_prefix.'boundary b ON (e.boundary_id = b.id) '
							. 'WHERE e.boundary_id > 0 '
							. 'AND e.boundary_id IS NOT NULL '
							. 'AND b.id = %d '
							. 'OR (b.parent_id = %d AND b.parent_id > 0) ';
			
			$entities = $db->query(sprintf($entities_query, $boundary_id, $boundary_id));
			// Any records?
			if ($entities->count() > 0)
			{
				// To hold the static entity ids
				$entity_ids = array();
				foreach ($entities as $entity)
				{
					$entity_ids[] = $entity->id;
				}
				
				// Split the ids array into a string
				$entity_ids = implode(",", $entity_ids);
				
				// Extra conditions - where the boundary_id column is NULL
				// Necessary because of the parent > child relationship of boundaries
				$sql .= 'OR (((i.boundary_id IS NULL AND i.static_entity_id IS NOT NULL) '
					. 'OR i.boundary_id IS NOT NULL) '
					. 'AND i.static_entity_id > 0 '
					. 'AND i.static_entity_id IN (%s)) ';
				
				// Apply string formatting
				$sql = sprintf($sql, $entity_ids);
			}
			
			// Group the incidents by id
			$sql .= 'GROUP BY i.id ';
			
			// Order the incidents by date in descending order
			$sql .= 'ORDER BY i.incident_date DESC';
			
			// Debug
			Kohana::log('debug', sprintf('Report fetch query for "%s" incidents: %s', $status, $sql));
			
			// Return
			return $db->query($sql);
		}
	}
}
?>
