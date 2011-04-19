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
	 * Agency id for the user's role
	 * 
	 * @var int
	 */
	protected $agency_id = 0;

	/**
	 * Admin boundary specific role
	 *
	 * @var boolean
	 */
	protected $boundary_role = FALSE;
	
	/**
	 * ID of the boundary to which the role has access
	 *
	 * @var int
	 */
	protected $boundary_id  = 0;

	/**
	 * Shows that the role is for a specific category
	 *
	 * @var boolean
	 */
	protected $category_role = FALSE;
	
	/**
	 * ID of the category to which the role access
	 *
	 * @var int
	 */
	protected $category_id = 0;

    /**
     * Constructor
     */
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
				
				$role_id = $this->role->id;

				// Check for static entity_id privilege
				$entity_privilege = Dashboard_Role_Model::has_static_entity_privilege($role_id);

				if ($entity_privilege)
				{
					$this->static_entity_role = TRUE;

					// Set the entity id
					$this->static_entity_id = json_decode($entity_privilege)->static_entity_id;
				}
				else
				{
				    // Get the other privileges
				    $this->_determine_role_privileges();
				}
			}
		}
		else
		{
			// User not logged in, redirect to the dashboards login page
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
	
	/**
	 * Helper function to determine the privileges of the the current user's role
	 *
	 * This function checks all other privileges other than the static entity id
	 * privilege
	 */
	private function _determine_role_privileges()
	{
	    // Fetch the role id
	    $role_id = $this->role->id;
	    
		// Agency-wide privilege
		$agency_privilege = Dashboard_Role_Model::has_agency_privilege($role_id);
		
		if ($agency_privilege)
		{
		    $this->agency_role = TRUE;
		    
		    // Decode the return value
		    $entity_privilege = json_decode($entity_privilege);
		    
		    // Set the agency and boundary id for the privilege
		    $this->agency_id = $entity_privilege->agency_id;
		    $this->boundary_id = $entity_privilege->boundary_id;
		    
		    // Halt
		    return;
		}
		
		// Category-wide privilege
		$category_privilege = Dashboard_Role_Model::has_category_privilege($role_id);
		
		if ($category_privilege)
		{
		    // Enable category role
		    $this->category_role = TRUE;
		    // Decode JSON
		    $category_privilege = json_decode($category_privilege);
		    
		    // Set the category and boundary to which the role has access
		    $this->category_id = $category_privilege->category_id;
		    $this->boundary_id = $category_privilege->boundary_id;
		    
		    // Halt
		    return;
		}
		
		// Boundary-wide privilege
		$boundary_privilege = Dashboard_Role_Model::has_boundary_privilege($role_id);
		
		if ($boundary_privilege)
		{
		    $this->boundary_role = TRUE;
		    
		    // Decode JSON
		    $boundary_privilege = json_decode($boundary_privilege);
		    
		    // Set the boudary id
		    $this->boundary_id = $boundary_privilege->boundary_id;
		    
		    // Halt (probably unnecessary as it's the last check anyway)
		    return;
		}
	    
	}
}
?>
