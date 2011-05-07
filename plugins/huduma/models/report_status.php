<?php defined('SYSPATH') or die ('No direct script access.');
/**
 * Model for the report_status table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Report Status Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Report_Status_Model extends ORM
{
	// Table name
	protected $table_name = 'report_status';
	
	/**
	 * Checks if the provided report status is valid and exists in the database
	 *
	 * @param int $status_id
	 * @return boolean
	 */
	public static function is_valid_report_status($status_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $status_id) > 0)
			? self::factory('report_status', $status_id)->loaded
			: FALSE;
	}
	
	/**
	 * Returns a list (key->value) the report status codes
	 *
	 * @return array
	 */
	public static function get_report_status_dropdown()
	{
		return self::factory('report_status')->select_list('id', 'status_name');
	}
}
?>