<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Static Entity Types Metadata 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Roles Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Type_Metadata_Model extends ORM {

	// Table name
	protected $table_name = 'static_entity_type_metadata';
	
	// Relationships
	protected $has_many = array('static_entity_type');


	/**
	 * Validates and optionally saves a static entity type metadata item from an array
	 *
	 * @param	array $array	Values to check
	 * @param	boolean $save	Save the record when validation succeeds
	 * @return	boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
					->pre_filter('trim', TRUE)
					->add_rules('metadata_item', 'required', 'length[4,35]')
					->add_rules('description', 'length[0,255]')
					->add_rules('static_entity_type_id', 'length[0,255]');

		return parent::validate($array, $save);
	}
	
	
	//	> HELPER METHODS


	/**
	 * Gets the list of static entity types for use in a dropdown list
	 * 
	 * @return array
	 */
	public static function get_entity_types_dropdown()
	{
		return self::factory('static_entity_type')->select_list('id', 'name');
	}


}
?>
