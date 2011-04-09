<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Administrative Boundary Types
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Boundary Type Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Boundary_Type_Model extends ORM {
    // Table name
    protected $table_name = 'boundary_type';

    // Relationships
    protected $has_many = array('boundary');


	/**
	 * Checks if the boundary type in @param $type_id exists in the database
	 * 
	 * @param int $type_id
	 * @return boolean
	 */
	public static function is_valid_boundary_type($type_id)
	{
		// Attempt to fetch the boundary from the database
		return is_int($type_id)
			? self::factory('boundary_type', $type_id)->loaded
			: FALSE;

	}
}
?>
