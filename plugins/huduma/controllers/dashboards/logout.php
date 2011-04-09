<?php
/**
 * This controller handles logout requests for the frontend dashboard
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Logout Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Logout_Controller extends Template_Controller {

	/**
	 * Disable auto rendering for this controller
	 * 
	 * @var boolean
	 */
	public $auto_render = FALSE;

	/**
	 * Template for this controller
	 * 
	 * @var string
	 */
	public $template = "";


	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Check if someone is logged in
		$auth_lite = Authlite::instance('authlite');

		if ($auth_lite->logged_in())
		{
			// Logout the user
			$auth_lite->logout();
		}
	}

	/**
	 * Landing page
	 */
	public function index()
	{
		// Redirect to the main page
		url::redirect('main');
	}
}
?>