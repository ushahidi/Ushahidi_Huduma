<?php defined('SYSPATH') or die('No direct script access.');

/**
* Model for Categories for each Incident
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Category Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Incident_Category_Model extends ORM
{
	protected $belongs_to = array('incident', 'category');
	
	// Database table name
	protected $table_name = 'incident_category';
	
	/**
	 * Validates the categories for an incident category entry
	 *
	 * @param   $array    array -  Data to be validated
	 * @param   $save     boolean
	 * @return  boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
	    $array = Validation::factory($array)
	                ->pre_filter('trim', TRUE)
	                ->add_rules('incident_id', 'required', array('Incident_Model', 'is_valid_incident'))
	                ->add_rules('category_id', 'required', array('Category_Model', 'is_valid_category'));
	    
	    // Return valiation result from parent
	    return parent::validate($array, $save);
	}
}
