<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Static Entity Type
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Type Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Type_Model extends ORM {
    // Table name
    protected $table_name = 'static_entity_type';

    // Relationships
    protected $has_many  = array('static_entity');
    
    // A static entity type must be associated with a category
    protected $belongs_to = array('category');

	/**
	 * Checks if the static entity type in @param $type_id exists in the database
	 * 
	 * @param int $type_id
	 * @return boolean
	 */
	public static function is_valid_static_entity_type($type_id)
	{
		// Validate numbers using regular expressions as the value may be in quotes ' or "
		return (preg_match('/^[1-9](\d*)$/', $type_id) > 0)
			? ORM::factory('static_entity_type', $type_id)->loaded
			: FALSE;
	}

}