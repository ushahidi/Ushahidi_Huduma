<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Locations
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Location Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Location_Model extends ORM
{
	protected $has_many = array('incident', 'media', 'incident_person', 'feed_item', 'reporter');
	protected $has_one = array('country');
	
	// Database table name
	protected $table_name = 'location';
	
	/**
	 * @return  boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
	    $array = Validation::factory($array)
	                ->pre_filter('trim', TRUE)
	                ->add_rules('latitude', 'required', 'between[-90,90]')
	                ->add_rules('longitude', 'required', 'between[-180,180]')
	                ->add_rules('location_name', 'required', 'length[3,200]');
        
	    return parent::validate($array, $save);
	}
	
	/**
	 * Checks if the specified location exists in the database
	 *
	 * @param   int $location_id
	 * @return  boolean
	 */
	public static function is_valid_location($location_id)
	{
	    return (preg_match('/^[1-9](\d*)$/', $location_id) > 0)
	        ? self::factory('location', $location_id)->loaded
	        : FALSE;
	}
	
}
