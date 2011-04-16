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
	
	/**
	 * Validates a comment entry before saving
	 *
	 * @param   array   $array Data to be validated
	 * @param   boolean $save
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
	    // Set up validation and add some rules
	    $array = Validation::factory($array)
	                ->pre_filter('trim', TRUE)
	                ->add_rules('static_entity_id', 'required', 'Static_Entity_Model::is_valid_static_entity')
	                ->add_rules('comment_author', 'required', 'length[3,100]')
	                ->add_rules('comment_email', 'required', 'email', 'length[4,100]')
	                ->add_rules('comment_description', 'required');
	                
	    return parent::validate($array, $save);
	}

}
?>
