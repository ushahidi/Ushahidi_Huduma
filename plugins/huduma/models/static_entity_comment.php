<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Model for the static entity comments table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Static Entity
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Static_Entity_Comment_Model extends ORM {

	// Table name
	protected $table_name = 'static_entity_comment';
	
	// Relationships
	protected $belongs_to = array('static_entity');

	protected $has_many = array('rating');

}
?>
