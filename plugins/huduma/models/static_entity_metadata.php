<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for static entity metadata
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Metadata Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Metadata_Model extends ORM
{
    
    // Relationships
    protected $belongs_to = array('static_entity');
    
    // Table name
    protected $table_name = 'static_entity_metadata';
    
    /**
     * Validates the data to be saved
     *
     * @param   array   $array  Data to be validated before saving
     * @param   boolean $save   Whether to save the data when validation succeeds
     * @return  boolean
     */
    public function validate(array & $array, $save = FALSE)
    {
        // Setup validation
        $array = Validation::factory($array)
                    ->pre_filter('trim')
                    ->add_rules('static_entity_id', 'required', 'Static_Entity_Model::is_valid_static_entity')
                    ->add_rules('item_label', 'required')
                    ->add_rules('item_value', 'required')
                    ->add_rules('as_of_year', 'required');
                    
        // Pass validation to parent and return
        return parent::validate($array, $save);
    }
    
    /**
     * Checks if the specified metadata item exists in the database
     *
     * @param   int $metadata_id
     * @return  boolean
     */
    public static function is_valid_static_entity_metadata($metadata_id)
    {
        return (preg_match('/^[1-9](\d*)$/', $metadata_id) > 0)
            ? self::factory('static_entity_metadata', $metadata_id)->loaded
            : FALSE;
    }
}
?>