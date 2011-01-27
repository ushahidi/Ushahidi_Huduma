<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Static Entity
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Model extends ORM {
    
    // Database table name
    protected $table_name = 'static_entity';

    // Relationships
    protected $belongs_to = array('static_entity_type');

    protected $has_many = array('static_entity_metadata');

    /**
     * Gets the list of static entities for the entity type specified in @param $type_id
     * @param int $type_id
     */
    public static function entities($type_id)
    {
        return ORM::factory('static_entity')
                        ->where('static_entity_type_id', $type_id)
                        ->find_all();
    }
}