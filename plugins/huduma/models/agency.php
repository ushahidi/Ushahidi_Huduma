<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Agencies
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Agency Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Agency_Model extends ORM {
    
	// Database table name
	protected $table_name = 'agency';

	// Relationships
	protected $has_many = array('agency_staff');

	protected $belongs_to = array('boundary');
	
	/**
	 * Validates and optionally saves a new agency record from an array
	 *
	 * @param array $array Values to check
	 * @param boolean $save Save record when validation succeeds
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Setup validation
		$array = Validation($array)
					->pre_filter('trim')
					->add_rules('agency_name', 'required')
					->add_rules('description', 'required')
					->add_rules('category_id', 'required', array('Category_Model', 'is_valid_category'));
		
		// Parent ID validation
		if ( ! empty($array->parent_id) AND $array->parent_id != 0)
		{
			$array->add_rules('parent_id', array('Agency_Model', 'is_valid_agency'));
		}
		
		// Boundary validation
		if ( ! empty($array->boundary_id) AND $array->boundary_id != 0)
		{
			$array->add_rules('boundary_id', array('Boundary_Model', 'is_valid_boundary'));
		}
		
		// Pass on valdation to parent and return
		return parent::validate($array, $save);
	}
	/**
	 * Gets the list of agencies that fall within the category specified in @param $category_id
	 * 
	 * @param int $category_id ID of the category
	 * @return array
	 */
	public static function get_agencies_by_category($category_id)
	{
		return Category_Model::is_valid_category($category_id)
			? ORM::factory('agency')
					->select('id', 'agency_name')
					->where('category_id', $category_id)

			: array();
	}

	/**
	 * Gets the list of service agencies for the boundary specified in @param $boundary_id
	 * 
	 * @param int $boundary_id
	 * @return array
	 */
	public static function get_agencies_by_boundary($boundary_id)
	{
		// If the boundary id is valid, fetch the agencies
		return Boundary_Model::is_valid_boundary($boundary_id)
			? self::factory('agency')
				->select('id', 'agency_name')
				->where('boundary_id', $boundary_id)
				
			: array();
	}

	/**
	 * Checks if the agency specified in @param $agency_id exists in the database
	 * 
	 * @param int $agency_id
	 * @return boolean
	 */
	public static function is_valid_agency($agency_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $agency_id) > 0)
				? self::factory('agency', $agency_id)->loaded
				: FALSE;
	}

	/**
	 * Gets an array of agencies for display in a dropdown list
	 * 
	 * @return array
	 */
	public static function get_agencies_dropdown()
	{
		return self::factory('agency')->select_list('id', 'agency_name');
	}
    
}
