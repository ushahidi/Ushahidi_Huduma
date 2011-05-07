<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Model for the report_priority table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Report Priority Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Report_Priority_Model extends ORM
{
	// Table name
	protected $table_name = 'report_priority';
	
	/**
	 * Checks if the provided report priority is valid and exists in the database
	 *
	 * @param int $priority_id
	 * @return boolean
	 */
	public static function is_valid_report_priority($priority_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $priority_id) > 0)
			? self::factory('report_priority', $priority_id)->loaded
			: FALSE;
	}
}
?>