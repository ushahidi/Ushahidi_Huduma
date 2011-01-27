<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Huduma Hook - Load All Events
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class huduma {

	/**
	 * Registers the main event add method
	 */

	public function __construct()
	{
		$this->db = Database::instance();

		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
		
	}
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		// Add a Sub-Nav Link
		Event::add('ushahidi_action.nav_main_right_tabs', array($this,'_huduma_link'));

		// Only add the events if we are on that controller
		if (Router::$controller == 'admin')
		{
			plugin::add_stylesheet('huduma/views/css/main');
		
		}
	}	


	public function _huduma_link()
	{

		$main_right_tabs = Event::$data;
		Event::$data = array_merge($main_right_tabs,array('huduma'=>'Huduma Addons'));
	}






}

new huduma;
