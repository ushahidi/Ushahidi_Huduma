<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Dashboard_Template_Controller extends Frontend_Controller {

	/**
	 * Currently logged in user
	 * 
	 * @var Dashboard_User
	 */
	protected $user;

	/**
	 * Role of the currently logged in user
	 *
	 * @var Dashboard_Role_Model
	 */
	protected $role;

	/**
	 * Table prefix - for straight DB queries
	 *
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * Authlite instance
	 *
	 * @var Authlite
	 */
	protected $auth_lite;

	/**
	 * Database instance
	 *
	 * @var Database
	 */

	/**
	 * Denotes that the role only has access to a static entity
	 * 
	 * @var boolean
	 */
	protected $static_entity_role = FALSE;

	/**
	 * Static entity id
	 *
	 * @var int
	 */
	protected $static_entity_id = 0;

	/**
	 * Shows that the role is for a specific agency
	 *
	 * @var boolean
	 */
	protected $agency_role = FALSE;

	/**
	 * Admin boundary specific role
	 *
	 * @var boolean
	 */
	protected $boundary_role = FALSE;

	/**
	 * Shows that the role is for a specific category
	 *
	 * @var boolean
	 */
	protected $category_role = FALSE;


	public function __construct()
	{
		parent::__construct();

		// Load Authlite
		$this->auth_lite = Authlite::instance('authlite');

		if ($this->auth_lite->logged_in())
		{
			// Set the user
			$this->user = $this->auth_lite->get_user();

			// Load the users role
			// Check if the user has a role
			if ( ! empty($this->user->dashboard_role_id))
			{
				// Set the role
				$this->role = $this->user->dashboard_role;

				// Check for static entity_id privilege
				$entity_privilege = Dashboard_Role_Model::has_static_entity_privilege($this->role->id);

				if ($entity_privilege)
				{
					$this->static_entity_role = TRUE;

					// Set the entity id
					$this->static_entity_id = json_decode($entity_privilege)->static_entity_id;
				}
				else
				{
					// Proceed to check for agency wide, category-wide, boundary privileges
				}

			}

		}
		else
		{
			// Redirect to the dashboards login page
			url::redirect('dashboards/login');
		}
	}

	/**
	 * Landing page for this controller
	 */
	public function index()
	{
		// Redirect to dashboards home page
		url::redirect('dashboards/home');
	}
}
?>
