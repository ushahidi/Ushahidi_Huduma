<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * This controller handles login requests for the frontend dashboard
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Login Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Login_Controller extends Template_Controller {
	
	public $auto_render = FALSE;

	protected $dashboard_user;

	// Session object
	protected $session;

	// Main template
	public $template = '';//'dashboard_login';

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->session = new Session();
	}

	/**
	 * Landing page for this controller
	 */
	public function index()
	{
		$auth = Authlite::instance('authlite');

		// If already logged in, redirect to the dashboard page
		if ($auth->logged_in())
		{
			Kohana::log('debug', 'User already logged in. Redirecting...');
		}

		// Set up form fields
		$form = array(
			'dashboard_username',
			'dashboard_password',
			'dashboard_login_remember'
		);

		// Copy the form as errors, so errors will be stored with keys corresponding to field names
		$errors = $form;
		$form_error = FALSE;

		// Has the form been submitted
		if ($_POST)
		{
			// Set up validation
			$validation = Validation::factory($_POST);

			// Add some filters
			$validation->pre_filter('trim')
						->add_rules('dashboard_username', 'required')
						->add_rules('dashboard_password', 'required');

			// Validate
			if ($validation->validate())
			{
				Kohana::log('debug', 'Validation succeeded!');
				
				// Set the remember flag
				$remember = isset($validation['dashboard_remember'])? TRUE : FALSE;
				
				// Attempt login
				if ($auth->login($validation->dashboard_username, $validation->dashboard_password, $remember))
				{
					Kohana::log('debug', sprintf('User %s successfully logged into the dashboard', $auth->get_user()));

					// Load the user, get the role and set it
					$user = $auth->get_user();

					Kohana::log('info', sprintf('Account status for user: %s', $user->is_active));

					// Check if the user is active
					if ($user->is_active AND ! empty($user->dashboard_role_id))
					{
						$this->session->set('dashboard_role', $user->dashboard_role_id);
						
						// Redirect to dashboards home page
						url::redirect('dashboards/home');
					}
					else
					{
						// User is in active
						Kohana::log('debug', sprintf('The account for user %s is inactive %s', $user->username, $user->is_active));

						// Logout the user
						$auth->logout();

						// Redirect to the home page
						url::redirect('main');
					}
				}
				else
				{
					Kohana::log('debug', sprintf('The specified username: %s does not exist', $validation->dashboard_username));
				}
			}
		}
		else
		{
			Kohana::log('debug', 'Redirecting to the home page...');
			
			url::redirect('main');
		}
	}
}
?>
