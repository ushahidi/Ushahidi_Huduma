<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for the boundaries tablex
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
					->add_rules('boundary_type', 'required', 'chars[1,2]');
		
		// Check if the parent id has been set
		if ( ! empty($array->parent_id) AND $array->parent_id != 0)
		{
			$array->add_rules('parent_id', array('Boundary_Model', 'is_valid_boundary'));
		}
		
		// Pass on validation to the parent
		return parent::validate($array, $save);
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
						->select_list('id',	'boundary_name')
						->where(array('parent_id' => $parent_id));
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
	public static function get_boundaries_dropdown()
	{
	    return self::factory('boundary')->select_list('id', 'boundary_name');
	}
}
?>
