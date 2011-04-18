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
	    
	    // Check if the dashboard user id is in the validation data
	    if ( ! empty($array->dashboard_user_id) AND $array->dashboard_user_id != 0)
	    {
	        // Ensure the dashboard user id is validated
	        $array->add_rules('dashboard_user_id', array('Dashboard_User_Model', 'is_valid_dashboard_user'));
	    }
	    
	    if ( ! empty($array->parent_comment_id) AND $array->parent_comment_id != 0)
	    {
	        $array->add_rules('parent_comment_id', array('Static_Entity_Comment_Model', 'is_valid_static_entity_comment'));
	    }
	    
	    return parent::validate($array, $save);
	}
	
	//> HELPER CLASS METHODS
	
	/**
	 * Checks if the specified comment id is valid and exists in the database
	 *
	 * @param   int $comment_id
	 * @return  boolean
	 */
	public static function is_valid_static_entity_comment($comment_id)
	{
	    return (preg_match('/^[1-9](\d*)$/', $comment_id) > 0)
	        ? self::factory('static_entity_comment', $comment_id)->loaded
	        : FALSE;
	}
	
	/**
	 * Gets the inline comments for the comment in @param $comment_id
	 *
	 * @param   int $comment_id
	 * @return  array
	 */
	public static function get_inline_comments($comment_id)
	{
	   if ( ! self::is_valid_static_entity_comment($comment_id))
	   {
	       return FALSE;
	   }
	   else
	   {
	       // Return the comments with @param $comment_id as the parent
    	   return self::factory('static_entity_comment')
    	                ->select('id', 'comment_author', 'comment_description', 'comment_date')
    	                ->where(array('parent_comment_id' => $comment_id, 'comment_spam' => 0, 'comment_active' => 1))
    	                ->orderby('comment_date', 'asc')
    	                ->find_all();
        }
	}

}
?>
