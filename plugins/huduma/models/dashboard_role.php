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
	protected $has_many = array('dasboard_user', 'dashboard_role_privileges');


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
					->add_rules('name', 'required', 'length[4,35]')
					->add_rules('description', 'length[0,255]');

		if  ($array->agency_id != 0)
		{
			// Check if the specified service agency exists
			$array->add_rules('agency_id', array('Agency_Model', 'is_valid_agency'));
		}

		return parent::validate($array, $save);
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

			// Delete all privileges for the role
			$db->delete('dashboard_role_privileges', array('dashboard_role_id' => $role_id));

			// Remove the role from all users; set dashboard_role_id = 0
			$db->update('dashboard_user', array('dashboard_role_id' => 0, 'is_active' => 0), array('dashboard_role_id' => $role_id));

			// Free the $db instance from memory
			unset ($db);

			// Delete the role from the DB
			self::factory('dashboard_role', $role_id)->delete();

			return TRUE;
		}
	}

	/**
	 * Gets the privileges for associated with the specified role
	 * @param <type> $role_id
	 * @return <type>
	 */
	public static function get_privileges($role_id)
	{
		if ( ! self::is_valid_dashboard_role($role_id))
		{
			return array();

		}
		else
		{
			// Database instance for this operation
			$database = new Database;

			// Get the privileges
			$items = $database->where('dashboard_role_id', $role_id)->get('dashboard_role_privileges');

			// To hold the return value
			$privileges = array();

			// Iterate over result set and fetch items into array
			foreach ($items as $item)
			{
				// Create privilege instance
				$privilege = new Dashboard_Role_Privileges_Model();

				// Set properties
				$privilege->dashboard_role_id = $item->dashboard_role_id;
				$privilege->static_entity_id = $item->static_entity_id;
				$privilege->category_id = $item->category_id;
				$privilege->boundary_id = $item->boundary_id;

				// Add to array
				$privileges[] = $privilege;
			}

			// Release objects from memory
			unset ($database);
			unset ($items);

			return $privileges;
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
			$database = new Database();

			$results = $database->where(array('dashboard_role_id' => $role_id, 'static_entity_id !=' => 0))
					->get('dashboard_role_privileges');

			if (count($results) > 0)
			{
				// Return json object
				return json_encode(array(
					'status' => TRUE,
					'static_entity_id' => $results[0]->static_entity_id
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
			// Role is valid
			
			// Database instance for this operation
			$database = new Database();

			// For agency privilege, agency_id MUST be non-zero and static_entity_id = 0
			$results = $database->from('dashboard_role_privileges')
								->where(array('dashboard_role_id' => $role_id, 'agency_id !=' => 0, 'static_entity_id' => 0))
								->get();

			// Fetch the data
			if (count($results) > 0)
			{
				return json_encode(array(
					'status' => TRUE,
					'agency_id' => $results[0]->agency_id,
					'boundary_id' => $results[0]->boundary_id
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
			// Database instance for the lookup
			$database = new Database();

			// Category ID must be non-zero and agency_id must be zero
			$results = $database->from('dahboard_role_privileges')
								->where(array('dahboard_role_id' => $role_id, 'category_id !=' => 0, 'agency_id' => 0))
								->get();

			// Records found?
			if (count($records) > 0)
			{
				// Success, return 
				return json_encode(array(
					'status' => TRUE,
					'category_id' => $results[0]->category_id,
					'boundary_id' => $results[0]->boundary_id
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
		if ( ! self::is_valid_dahboard_role($role_id))
		{
			return FALSE;
		}
		else
		{
			// Success, proceed

			// Database instance
			$database = new Database();

			$results = $database->from('dashboard_role_privileges')
				->where(array(
					'dashboard_role_id' => $role_id, 
					'boundary_id !=' => 0, 
					'category_id' => 0, 
					'agency_id' => 0
				))
				->get();

			// Check if records have been returned
			if (count($records) > 0)
			{
				return json_encode(array(
					'status' => TRUE,
					'boundary_id' => $results[0]->boundary_id
				));
			}
			else
			{
				return FALSE;
			}
		}
	}
}
?>
