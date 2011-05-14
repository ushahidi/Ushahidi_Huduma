<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Categories of reported Incidents
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Category Model  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Category_Model extends ORM_Tree
{	
	protected $has_many = array('incident' => 'incident_category', 'category_lang');
	
	// Database table name
	protected $table_name = 'category';
	protected $children = "category";
	
	static function categories($id=NULL,$locale='en_US')
	{
		if($id == NULL){
			$categories = ORM::factory('category')->where('locale',$locale)->find_all();
		}else{
			$categories = ORM::factory('category')->where('id',$id)->find_all(); // Don't need locale if we specify an id
		}
		
		$cats = array();
		foreach($categories as $category) {
			$cats[$category->id]['category_id'] = $category->id;
			$cats[$category->id]['category_title'] = $category->category_title;
			$cats[$category->id]['category_color'] = $category->category_color;
			$cats[$category->id]['category_image'] = $category->category_image;
			$cats[$category->id]['category_image_thumb'] = $category->category_image_thumb;
		}
		
		return $cats;
	}

	/**
	 * Checks if the category specified in @param $category_id is valid and exists
	 * and exists in the database
	 * 
	 * @param int $category_id
	 * @return boolean
	 */
	public static function is_valid_category($category_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $category_id) > 0)
			? ORM::factory('category', $category_id)->loaded
			: FALSE;
	}

	/**
	 * Gets the list of active categories into an array to be used within a dropdown list
	 *
	 * @param	voolean $parent_only
	 * @return	array
	 */
	public static function get_dropdown_categories($parent_only = TRUE)
	{
		return ($parent_only)
			? self::factory('category')
				->where(array('parent_id' => 0, 'category_visible' => 1))
				->select_list('id', 'category_title')
			: self::factory('category')
				->where('category_visible', 1)
				->select_list('id', 'category_title');
	}
}
