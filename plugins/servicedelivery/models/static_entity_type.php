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
    protected $has_many  = array('static_entity', 'static_entity_type_metadata');

}