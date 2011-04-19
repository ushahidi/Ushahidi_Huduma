<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Administrative Boundaries
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Administrative Boundary Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Boundary_Model extends ORM {
    // Table name
    protected $table_name = 'boundary';

    // Relationships
    protected $belongs_to = array('boundary_type');


	/**
	 * Helper method to get the list of boundaries whose parent id is specified
	 * in @param $parent_id
	 *
	 * @param int $parent_id
	 * @param boolean $include_type_name
	 * @return array
	 */
	public static function get_boundaries_list($parent_id = FALSE, $include_type_name = TRUE)
	{
		// To hold the return list
		$boundaries = array();

		// Fetch the boundary objects
		$orm_iterator = ($parent_id == TRUE AND self::is_valid_boundary($parent_id))
			? ORM::factory('boundary')
					->where('parent_id', $parent_id)
					->find_all()

			: ORM::factory('boundary')->find_all();

		// Fetch the items in the iterator into $boundaries
		foreach ($orm_iterator as $item)
		{
			// $boundary_type_name == TRUE, include name of the boundary type
			$boundaries[$item->id] = $item->boundary_name.
					(($include_type_name)? ' '.$item->boundary_type->boundary_type_name : '');
		}

		// Return
		return $boundaries;

	}

	/**
	 * Gets the list of boundaries whose boundary type in @param $type_id
	 *
	 * @param int $type_id
	 * @return array
	 */
	public static function get_boundaries_list_by_type($type_id)
	{
		return Boundary_Type_Model::is_valid_boundary_type($type_id)
			? self::factory('boundary')
				->select('id', 'boundary_name')
				->where('boundary_type_id', $type_id)
				->find_all()

			: array();

	}

	/**
	 * Checks if the boundary in @param $boundary_id exists in the database
	 * @param int $boundary_id
	 * @return boolean
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
	public static function get_boundaries_dropdown()
	{
	    return self::factory('boundary')->select_list('id', 'boundary_name');
	}
}
?>
