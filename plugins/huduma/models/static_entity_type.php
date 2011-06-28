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
	 * Validates and optionally saves a static entity type record from an array
	 *
	 * @param array $array Values to check
	 * @param boolean $save Save the record when validation succeeds
	 * @param return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Set up validation
		$array = Validation::factory($array)
					->pre_filter('trim')
					->add_rules('type_name', 'required')
					->add_rules('category_id', 'required', array('Category_Model', 'is_valid_category'))
					->add_rules('entity_type_color', 'required', 'length[6,6]');
		
		// Pass validation to parent			
		return parent::validate($array, $save);
	}
	
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
			? self::factory('static_entity_type', $type_id)->loaded
			: FALSE;
	}
	
	/**
	 * Gets a key=>value array of the static entity types where the key is the id and value
	 * is the type name of the static entity type
	 *
	 * @return  array
	 */
	public static function get_entity_types_dropdown($category_id = NULL)
	{
	    return (Category_Model::is_valid_category($category_id))
			? self::factory('static_entity_type')->where('category_id', $category_id)->select_list('id', 'type_name')
			: self::factory('static_entity_type')->select_list('id', 'type_name');
	}

}