<?php defined('SYSPATH') or die('No direct script access.');

/**
* Comments Table Model
*/

class Comment_Model extends ORM
{
	protected $has_many = array('rating');
	protected $belongs_to = array('incident');
	
	// Database table name
	protected $table_name = 'comment';
	
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
	                ->add_rules('incident_id', 'required', 'Incident_Model::is_valid_incident')
	                ->add_rules('comment_author', 'required', 'length[3,100]')
	                ->add_rules('comment_email', 'required', 'email', 'length[4,100]')
	                ->add_rules('comment_description', 'required');
	    
	    // Run validation event
	    Event::run('ushahidi_action.orm_validate_comment', $array);
	    
	    return parent::validate($array, $save);
	}
	
	//> HELPER CLASS METHODS
	
	/**
	 * Checks if the specified comment id is valid and exists in the database
	 *
	 * @param   int $comment_id
	 * @return  boolean
	 */
	public static function is_valid_comment($comment_id)
	{
	    return (preg_match('/^[1-9](\d*)$/', $comment_id) > 0)
	        ? self::factory('comment', $comment_id)->loaded
	        : FALSE;
	}
}