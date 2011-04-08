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

    /**
     * Gets the list of static entities for the entity type specified in @param $type_id
	 * 
     * @param int $type_id
     */
    public static function get_entities_by_type($type_id)
    {
        return ORM::factory('static_entity')
                        ->where('static_entity_type_id', $type_id);
//                        ->find_all();
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
}