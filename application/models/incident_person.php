<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for people who reported each Incident
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Person Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Incident_Person_Model extends ORM
{
	protected $belongs_to = array('location', 'incident');
	
	// Database table name
	protected $table_name = 'incident_person';
	
	/**
	 * Validates data on the person reporting the incident
	 *
	 * @param       $array  array
	 * @param       $save   boolean
	 * @return      boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
	    $array = Validation::factory($array)
	                ->pre_filter('trim');
	                
	    // Validate incident
	    if (isset($array->incident_id))
	    {
	        $array->add_rules('incident_id', array('Incident_Model', 'is_valid_incident'));
	    }
	    
	    // Validate location
	    if (isset($array->location_id))
	    {
	        $array->add_rules('location_id', array('Location_Model', 'is_valid_location'));
	    }
	    
	    // Validate first name
	    if ( ! empty($array->person_first))
	    {
	        $array->add_rules('person_first', 'length[3,100]');
	    }
	    
	    // Validate last name
	    if ( ! empty($array->person_last))
	    {
	        $array->add_rules('person_last', 'length[3,100]');
	    }
	    
	    // Validate email address
	    if ( ! empty($array->person_email))
	    {
	        $array->add_rules('person_email', 'email', 'length[3,100]');
	    }
	                
	    // Return validation results from parent
	    return parent::validate($array, $save);
	}
}
