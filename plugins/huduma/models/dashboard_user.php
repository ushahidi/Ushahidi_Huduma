<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Model for Dashboard Users
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboard Users Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Dashboard_User_Model extends ORM {

	//	Table name
	protected $table_name = 'dashboard_user';

	// Relationships
	protected $belongs_to = array('dashboard_role');

	public function __set($key, $value)
	{
		// Ensure the user's password is hashed
		if ($key == 'password')
		{
			$value = Authlite::instance()->hash($value);
		}

		parent::__set($key, $value);
	}

	/**
	 * Model validation
	 *
	 * @param array $array
	 * @param boolean $save
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Initialize the validation library and setup some rules
		$array = Validation::factory($array)
			->pre_filter('trim', TRUE)
			->add_rules('name', 'required')
			->add_rules('email', 'required', 'email')
			->add_rules('is_active', 'between[0,1]')
			->add_callbacks('email', array($this, 'email_exists'));

		// A new user, add validation rules for username and email
		if (empty($array->id))
		{
			$array->add_rules('username', 'required', array($this, '_username_exists'));
			
		}

		// Check if the password has been specified
		if ( ! empty($array->password))
		{
			// Password validation rule
			$array->add_rules('password', 'required', 'alpha_numeric', 'matches[confirm_password]');
		}

		// Validation for the dashboard role
		if (empty($array->dashboard_role_id) OR $array->dashboard_role_id == '0')
		{
			$array->is_active = 0;
		}
		elseif ($array->dashboard_role_id != '0')
		{
			$array->add_rules('dashboard_role_id', array('Dashboard_Role_Model', 'is_valid_dashboard_role'));
		}

		// Validate method in parent will set the properties
		return parent::validate($array, $save);
	}

	/**
	 * Checks if an email address has already been registered to another user
	 * 
	 * @param Validation $validation
	 */
	public function email_exists($email)
	{
		// Check if the specified email address has already been taken up
		$where = array('email' => $this->email);

		// If the user id has been set, add it to the list of predicates
		if (isset($this->id) AND !empty($this->id))
		{
			$where = array_merge($where, array('id != ' => $this->id));
		}

		return (bool) !$this->db->where($where)->count_records($this->table_name);
	}

	/**
	 * Checks if the username in the data being validated exists in the database
	 *
	 * @param Validation $validation
	 */
	public function _username_exists($username)
	{
		// Build the where clause for the search
		$where = array('username' => $username);

		// If the user id has been set, add it to the list of predicates
		if (isset($this->id) AND !empty($this->id))
		{
			$where = array_merge($where, array('id != ' => $this->id));
		}


		// Get the no. of records that have the current username on record
		return (bool) !$this->db->where($where)->count_records($this->table_name);
	}

	/**
	 * Checks if the specified dashboard user exists in the database
	 * 
	 * @param int $dashboard_user_id
	 * @return boolean
	 */
	public static function is_valid_dashboard_user($dashboard_user_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $dashboard_user_id) > 0)
				? self::factory('dashboard_user', $dashboard_user_id)->loaded
				: FALSE;
	}

	/**
	 * Gets the user whose username or email matches the one specified in @param $username
	 *
	 * @param	string $username
	 * @return	Dashboard_User_Model
	 */
	public static function find_user($username)
	{
		return self::factory('dashboard_user')->orwhere(
				array('username'=>$username, 'email'=>$username)
			);
	}

}
?>
