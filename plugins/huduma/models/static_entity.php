<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Static Entity
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Model extends ORM {
    
	// Database table name
	protected $table_name = 'static_entity';

	// Relationships
	protected $belongs_to = array('static_entity_type');
	
	protected $has_many = array('static_entity_metadata');

    /**
     * Validates the static entity data against the specific set of rules
     *
     * @param   array   $array
     * @param   boolean $save
     * @return  boolean
     */
    public function validate(array & $array, $save = FALSE)
    {
        $array = Validation::factory($array)
                    ->pre_filter('trim')
                    ->add_rules('entity_name', 'required')
                    ->add_rules('longitude', 'required', 'numeric')
                    ->add_rules('latitude', 'required', 'numeric');
        
        // Validate the ID for update operations
        if (isset($array->id))
        {
            $array->add_rules('id', array('Static_Entity_Model', 'is_valid_static_entity'));
        }
        
        // Validate admin boundary
        if (isset($array->boundary_id) AND $array->boundary_id != 0)
        {
            $array->add_rules('boundary_id', array('Boundary_Model', 'is_valid_boundary'));
        }
        
        // Valiadate service agency
        if (isset($array->agency_id) AND $array->agency_id != 0)
        {
            $array->add_rules('agency_id', array('Agency_Model', 'is_valid_agency'));
        }
        
        // Validate entity type
        if (isset($array->static_entity_type_id) AND $array->static_entity_type_id != 0)
        {
            $array->add_rules('static_entity_type_id', array('Static_Entity_Type_Model', 'is_valid_static_entity_type'));
        }
        
        return parent::validate($array, $save);
    }
    
	/**
	 * Gets the list of static entities for the specific entity type
	 * 
	 * @param int $type_id Database ID of the static entity type
	 * @return array List of a static entities
	 */
	public static function get_entities_by_type($type_id, $boundary_id = FALSE)
	{
		
		// Check if the boundary id has been specified
		if (Boundary_Model::is_valid_boundary($boundary_id))
		{
			// Fetch the database IDs for all boundaries with $boundary_id as parent
			$table_prefix = Kohana::config('database.default.table_prefix');
			$query = "SELECT se.id, se.entity_name "
				. "FROM ".$table_prefix."boundary b, ".$table_prefix."static_entity se "
				. "INNER JOIN ".$table_prefix."static_entity_type t ON (se.static_entity_type_id = t.id) "
				. "WHERE (se.boundary_id = b.id OR se.boundary_id = b.parent_id) "
				. "AND (b.id = %d OR b.parent_id = %d) "
				. "AND t.id = %d";
				
			return Database::instance()
					->query(sprintf($query, $boundary_id, $boundary_id, $type_id));
		}
		else
		{
			return (Static_Entity_Type_Model::is_valid_static_entity_type($type_id))
				? ORM::factory('static_entity')
						->where('static_entity_type_id', $type_id)
						->select('id', 'entity_name')
				: FALSE;
		}
		
	}

	/**
	 * Gets the list of static entities within the administrative boundary in @param $boundary_id
	 * 
	 * @param int $boundary_id
	 * @return array
	 */
	public static function get_entities_by_boundary($boundary_id)
	{
		return (Boundary_Model::is_valid_boundary($boundary_id))
			? ORM::factory('static_entity')
				->where('boundary_id', $boundary_id)
				->find_all()
			: array();
	}

	/**
	 * Fetches the list of static entities into an array that can be used in a dropdown list
	 * on the UI
	 * 
	 * @return array
	 */
	public static function get_entities_dropdown($category_id)
	{
		// Debug
		Kohana::log('debug', sprintf('Getting entities for category %d', $category_id));

		// Validation
		if ($category_id == 0 OR !Category_Model::is_valid_category($category_id))
		{
			return self::factory('static_entity')->select_list('id', 'entity_name');
		}
		else
		{
			// Database instance for this operation
			$database = new Database();

			// Fetch the entities within the specified category
			$result = $database->from('static_entity')
						->select('static_entity.id', 'static_entity.entity_name')
						->join('static_entity_type', 'static_entity.static_entity_type_id', 'static_entity_type.id', 'INNER')
						->where('static_entity_type.category_id', $category_id)
						->get();


			// Debug
			Kohana::log('debug', sprintf('Fetched %d entities for category %d', count($result), $category_id));

			// To hold the return data
			$entities = array();

			foreach ($result as $entity)
			{
				$entities[$entity->id] = $entity->entity_name;
			}

			// Free the database instance
			unset ($database);

			return $entities;
		}
	}

	/**
	 * Checks if the static entity specified in @param $entity_id exists in the database
	 * 
	 * @param int $entity_id
	 * @return boolean
	 */
	public static function is_valid_static_entity($entity_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $entity_id) > 0)
			? self::factory('static_entity', $entity_id)->loaded
			: FALSE;
	}

	/**
	 * Gets the name of the static entity
	 *
	 * @param	int $entity_id
	 * @return	string
	 */
	public static function get_entity_name($entity_id)
	{
		return self::factory('static_entity', $entity_id)->entity_name;
	}

	/**
	 * Gets the reports for a specific static entity
	 *
	 * @param int $entity_id
	 * @param string $status Status of the reports to fetch
	 * @return  ORM_Iterator
	 */
	public static function get_reports($entity_id, $status = 'all', $offset = FALSE, $limit = FALSE)
	{
	    if ( ! self::is_valid_static_entity($entity_id))
	    {
	        return FALSE;
	    }
	    else
	    {
			// Database instance
			$db = new Database();
			$table_prefix = Kohana::config('database.table_prefix');
			
			// Query to be executed
			$sql = 'SELECT i.id, i.incident_title, i.incident_description, i.incident_date, i.incident_mode, '
				. 'COUNT(co.id) AS comment_count '
				. 'FROM '.$table_prefix.'incident i '
				. 'LEFT JOIN '.$table_prefix.'comment co ON (co.incident_id = i.id) ';
			
			// Check if the status has been specified
			if ($status == 'resolved' OR $status == 'unresolved')
			{
				$sql .= 'LEFT JOIN '.$table_prefix.'incident_ticket it ON (it.incident_id = i.id) ';
				$sql .= (strtolower($status) == 'resolved')
					? 'WHERE it.report_status_id = 2 '
					: 'WHERE it.report_status_id = 1 ';
			}
			else
			{
				$sql .= 'LEFT JOIN '.$table_prefix.'incident_ticket it ON (it.incident_id = i.id) '
					. 'WHERE 1=1 ';
			}
			
			$sql .= 'AND i.static_entity_id = %d '
				. 'AND i.incident_active = 1 '
				. 'GROUP BY i.id '
				. 'ORDER BY i.incident_date DESC ';
			
			// Check if the limit and offset have been specified and are scalar values
			if ( is_int($offset) AND (int)$offset >= 0 AND is_int($limit) AND (int)$limit >= 1)
			{
				$sql .= 'LIMIT '.$offset.', '.$limit;
			}
				
	        // Return list of reports
	        return $db->query(sprintf($sql, $entity_id));
	    }
    }

    /**
     * Gets the comments for the specified report
     * @param   int     $incident_id
     * @return  ORM_Iterator
     */
	public static function get_comments($incident_id)
	{
		if ( ! Incident_Model::is_valid_incident($incident_id))
		{
			return FALSE;
		}
		else
		{
		    // Build the where clause
		    $where_clause = array(
		        'incident_id' => $incident_id,
		        'comment_active' => 1,
		        'comment_spam' => 0,
		    );
		    
			// Return array of comments
			return self::factory('comment')
						->where($where_clause)
						->orderby('comment_date', 'asc')
						->find_all();
		}
	}
	
	/**
	 * Returns the metadata for a static entity
	 *
	 * @param   int $entity_id ID of the static entity
	 * @return  array
	 */
	public static function get_metadata($entity_id)
	{
	    if ( ! self::is_valid_static_entity($entity_id))
	    {
	        return FALSE;
	    }
	    else
	    {
	        // Get the metadata
	        return self::factory('static_entity_metadata')
	                    ->where(array('static_entity_id' => $entity_id))
	                    ->orderby('static_entity_id', 'asc')
	                    ->find_all();
	    }
	}
	
	/**
	 * Creates a location object for the specified static entity
	 * This helper method is useful when submitting a report for a specific
	 * static entity
	 *
	 * @return  Location_Model
	 */
	public static function get_as_location($entity_id)
	{
	    if ( ! self::is_valid_static_entity($entity_id))
	    {
	        return FALSE;
	    }
	    else
	    {
	        // Load the static entity
	        $static_entity = self::factory('static_entity', $entity_id);
	        
	        // Extract entity data to be passed on for validation
	        $data = array(
	            'location_name' => $static_entity->entity_name,
	            'latitude' => $static_entity->latitude,
	            'longitude' => $static_entity->longitude
	        );
	        
	        // Location_Model instance to be returned
	        $location = new Location_Model();
	        
	        // Return
	        return ($location->validate($data))? $location : FALSE;
	    }
	}
	
	/**
	 * Gets the list of facilities neighbouring the specified facility. These facilities
	 * may not be of the same category as the specified facility
	 *
	 * @param int $entity_id Database ID for which to get neighbouring facilities
	 * @param int $count No. of neighbouring facilities to fetch
	 * @return mixed Returns Result when successful, FALSE on failure
	 */
	public static function get_neighbour_entities($entity_id, $count = 5)
	{
		// Validation
		if (self::is_valid_static_entity($entity_id))
		{
			// Get the table prefix
			$table_prefix = Kohana::config('database.table_prefix');
			
			// Load the static entity
			$entity = ORM::factory('static_entity', $entity_id);
			
			// SQL
			$query = "SELECT DISTINCT se.id, se.entity_name, t.type_name, "
					. "((ACOS(SIN(%s * PI() / 180) * SIN(se.`latitude` * PI() / 180) + COS(%s * PI() / 180) * COS(se.`latitude` * PI() / 180) "
					. "* COS((%s - se.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance "
					. "FROM `".$table_prefix."static_entity` AS se "
					. "INNER JOIN `".$table_prefix."static_entity_type` t ON (se.static_entity_type_id = t.id) "
					. "WHERE se.id != ".$entity_id." "
					. "ORDER BY distance ASC LIMIT %d";
			
			// Format the query
			$query = sprintf($query, $entity->latitude, $entity->latitude, $entity->longitude, $count);
			
			// Execute query
			$db = new Database();
			return $db->query($query);
		}
		else
		{
			return FALSE;
		}
	}
	
	/**
	 * Gets the list of static entities by category
	 * 
	 * @param int $category_id Database ID of the category to be used for filtering out the entities
	 * @return mixed ORM_Iterator if the category id is valid, FALSE if the category id is invalid
	 */
	public static function get_entities_by_category($category_id)
	{
		if (Category_Model::is_valid_category($category_id))
		{
			// Get the entity types
			$types = ORM::factory('static_entity_type')->where('category_id', $category_id)->find_all();
			$type_ids = array();
			foreach ($types as $type)
			{
				$type_ids[] = $type->id;
			}
			
			return ORM::factory('static_entity')->in('static_entity_type_id', $type_ids)->find_all();
		}
		else
		{
			return FALSE;
		}
	}
}