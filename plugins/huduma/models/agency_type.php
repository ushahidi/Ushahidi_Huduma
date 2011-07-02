<?php
/**
 * Model for the agency_type table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Huduma - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Agency_Type_Model extends ORM {
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'agency_type';
	
	/**
	 * One-to-many relationship definition
	 * @var array
	 */
	protected $has_many = array('agency');
	
	/**
	 * Checks and optionally saves an agency type record from an array
	 *
	 * @param array $array Values to check
	 * @param bool $save Saves the record when validation succeeds
	 * @return bool
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Set up validation
		$array = Validation::factory($array)
				->pre_filter('trim')
				->add_rules('type_name', 'required')
				->add_rules('short_name', 'required');
		
		// Pass validation to parent and return
		return parent::validate($array, $save);
	}
	
	/**
	 * Returns a list of the agency types as a key => value array
	 *
	 * @return array
	 */
	public static function get_agency_types_dropdown()
	{
		return ORM::factory('agency_type')->select_list('id', 'short_name');
	}
	
	/**
	 * Checks if an agency type exists in the database
	 *
	 * @param int $agency_type_id Database of the agency type to check
	 * @return bool TRUE if the agency type exists, FALSE otherwise
	 */
	public static function is_valid_agency_type($agency_type_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $agency_type_id) > 0)
		? ORM::factory('agency_type', $agency_type_id)->loaded
		: FALSE;
	}
}
?>