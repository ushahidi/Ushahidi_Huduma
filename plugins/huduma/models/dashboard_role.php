<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Dashboard Roles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Roles Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Dashboard_Role_Model extends ORM {

	// Table name
	protected $table_name = 'dashboard_role';
	
	// Relationships
	protected $has_many = array('dashboard_user');


	/**
	 * Validates and optionally saves a dashboard role from an array
	 *
	 * @param	array $array	Values to check
	 * @param	boolean $save	Save the record when validation succeeds
	 * @return	boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		$array = Validation::factory($array)
					->pre_filter('trim', TRUE)
					->add_rules('name', 'required', 'length[4,35]', array($this, 'role_name_exists'))
					->add_rules('description', 'length[0,255]')
					->add_rules('can_close_issue', 'required', 'length[1,1]');

		if  ( ! empty($array->agency_id) AND $array->agency_id != 0)
		{
			// Check if the specified service agency exists
			$array->add_rules('agency_id', array('Agency_Model', 'is_valid_agency'));
		}
		
		// Check if role is associated with a service agency
		if ($array->agency_id == 0 AND $array->category_id == 0 AND $array->boundary_id == 0 AND $array->static_entity_id = 0)
		{
			// Role not associated with agency, category, boundary or entity
			Kohana::log('error', 'No privileges defined');
			
			// Add validation errors for all items in the privilege matrix
			$array->add_error('agency_id', 'privileges');
			$array->add_error('category_id', 'privileges');
			$array->add_error('boundary_id', 'privileges');
			$array->add_error('static_entity_id', 'privileges');
		}
		
		// Category validation
		if ( ! empty($array->category_id)  AND $array->category_id != 0)
		{
			$array->add_rules('category_id', array('Category_Model', 'is_valid_category'));
			$array->agency_id = NULL;
		}

		// Administrative boundary validation
		if ( ! empty($array->boundary_id) AND $array->boundary_id != 0)
		{
			$array->add_rules('boundary_id', array('Boundary_Model', 'is_valid_boundary'));
		}

		// Either category or boundary specified, entity level access is irrelevant
		if ( ! empty($array->category_id) AND ! empty($array->boundary_id))
		{
			if ($array->category_id != 0 AND $array->boundary_id != 0)
			{
				$array->static_entity_id = NULL;
				$array->agency_id = NULL;
			}
		}
		
		// Static entity
		if ( ! empty($array->static_entity_id) AND $array->static_entity_id != 0)
		{
			// Add validation rule for static entity
			$array->add_rules('static_entity_id', array('Static_Entity_Model', 'is_valid_static_entity'));

			// Set
			$array->category_id = NULL;
			$array->boundary_id = NULL;
		}

		return parent::validate($array, $save);
	}
	
	/**
	 * Checks if the name of the current role already exists in the database
	 *
	 * @return boolean
	 */
	public function role_name_exists()
	{
		$where = array('name' => $this->name);
		if ( ! empty($this->id))
		{
			$where = array_merge($where, array('id !=' => $this->id));
			
		}
		return !(bool) ORM::factory($this->table_name)->where($where)->find_all()->count();
	}


	
//	> HELPER METHODS


	/**
	 * Gets the list of roles for use in a dropdown list
	 * 
	 * @return array
	 */
	public static function get_dropdown_roles()
	{
		return self::factory('dashboard_role')->select_list('id', 'name');
	}

	/**
	 * Checks if the specified role exists in the database
	 * 
	 * @param	int $role_id
	 * @return	 boolean
	 */
	public static function is_valid_dashboard_role($role_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $role_id) > 0)
			? self::factory('dashboard_role', $role_id)->loaded
			: FALSE;
	}

	/**
	 * Deletes a dashboard role from the database
	 * 
	 * @param	int $role_id
	 * @return	boolean
	 */
	public static function remove_role($role_id)
	{
		if (! self::is_valid_dashboard_role($role_id))
		{
			return FALSE;
		}
		else
		{
			// New database instance for this operation
			$db = new Database();

			// Remove the role from all users; set dashboard_role_id = NULL
			$db->update('dashboard_user', array('dashboard_role_id' => NULL, 'is_active' => 0), array('dashboard_role_id' => $role_id));

			// Free the $db instance from memory
			unset ($db);

			// Delete the role from the DB
			self::factory('dashboard_role', $role_id)->delete();

			return TRUE;
		}
	}

	/**
	 * Checks of the specified role has rights to a specific static entity
	 * 
	 * @param	int $role_id
	 * @return	boolean
	 */
	public static function has_static_entity_privilege($role_id)
	{
		if ( !self::is_valid_dashboard_role($role_id))
		{
			// Invalid role, FAIL
			return FALSE;
		}
		else
		{
			// Valid role
			$role = self::factory('dashboard_role', $role_id);
						
			if ( ! empty($role->static_entity_id) 
				AND Static_Entity_Model::is_valid_static_entity($role->static_entity_id)
			)
			{
				// Return json object
				return json_encode(array(
					'status' => TRUE,
					'static_entity_id' => $role->static_entity_id
				));
			}
			else
			{
				// No records found, FAIL
				return FALSE;
			}
		}
	}

	/**
	 * Checks if the specified role has agency level access
	 *
	 * @param	int	$role_id
	 * @return	boolean
	 */
	public static function has_agency_privilege($role_id)
	{
		// Validate role
		if ( ! self::is_valid_dashboard_role($role_id))
		{
			// Invalid dashboard role
			return FALSE;
		}
		else
		{
			// Load the current role
			$role = self::factory('dashboard_role', $role_id);
			
			// Check if the role has agency
			if ( ! empty($results->agency_id) AND Agency_Model::is_valid_agency($role->agency_id))
			{
				return json_encode(array(
					'status' => TRUE,
					'agency_id' => $role->agency_id,
					'boundary_id' => $role->boundary_id
				));
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Checks if the specified role has category privileges
	 *
	 * @param	int	$role_id
	 * @return 	boolean
	 */
	public static function has_category_privilege($role_id)
	{
		if ( ! self::is_valid_dashboard_role($role_id))
		{
			return FALSE;
		}
		else
		{
			// Category ID must be non-zero and agency_id must be zero
			$role = self::factory('dashboard_role', $role_id);

			// Records found?
			if ( ! empty($role->category_id) AND Category_Model::is_valid_category($role->category_id))
			{
				// Success, return 
				return json_encode(array(
					'status' => TRUE,
					'category_id' => $role->category_id,
					'boundary_id' => $role->boundary_id
				));
			}
			else
			{
				return FALSE;
			}
		}
	}

	/**
	 * Checks if the specified role has a boundary privilege
	 * This check only applies where the role has been granted access
	 * to a specific boundary - without agency or category restriction
	 *
	 * @param	int	$role_id
	 * @return	boolean
	 */
	public static function has_boundary_privilege($role_id)
	{
		// Validate specified role
		if ( ! self::is_valid_dashboard_role($role_id))
		{
			return FALSE;
		}
		else
		{
			$role = self::factory('dashboard_role', $role_id);

			// Check if records have been returned
			if ( ! empty($role->boundary_id) AND Boundary_Model::is_valid_boundary($role->boundary_id))
			{
				return json_encode(array(
					'status' => TRUE,
					'boundary_id' => $role->boundary_id
				));
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	/**
	 * Retrives a role from the database using the role name
	 *
	 * @param string $role_name Name of the role to be retrieved
	 * @return Dashboard_Role_Model
	 */
	public static function get_role_by_name($role_name)
	{
		$roles = self::factory('dashboard_role')->where('name', $role_name)->find_all();
		if ($roles->count() == 0 OR $role->count() > 1)
		{
			return FALSE;
		}
		else
		{
			$role_items = $roles->as_array();
			return $role_items[0];
		}
	}
}
?>
