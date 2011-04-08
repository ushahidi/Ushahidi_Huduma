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
