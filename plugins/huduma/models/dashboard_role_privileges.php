<?php defined('SYSPATH') or die('No direct script access.');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Dashboard_Role_Privileges_Model extends ORM {

	// Table name
	protected $table_name = 'dashboard_role_privileges';
	
	// Relationships
	protected $belongs_to = array('dashboard_role');

	/**
	 * Validates an optionally saves a role privilege
	 *
	 * @param	array	$array
	 * @param	boolean $save
	 * @return	boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Set up validation
		$array = Validation::factory($array)
				->pre_filter('trim', TRUE)
				->add_rules('dashboard_role_id', 'required', array('Dashboard_Role_Model', 'is_valid_dashboard_role'));

		// Get the parent dashboard role
		$dashboard_role = ORM::factory('dashboard_role', $array->dashboard_role_id);

		Kohana::log('debug', sprintf('Dashboard role id: %s', $dashboard_role->id));
		
		// Check if role is associated with a service agency
		if ($dashboard_role->agency_id == 0 AND ($array->category_id == 0 OR $array->boundary_id == 0) )
		{
			// Role not associated with agency, ensure privilege has at least a boundary or category
			$array->add_rules('category_id', 'required', array('Category_Model', 'is_valid_category'));
			$array->add_rules('boundary_id', 'required', array('Boundary_Model', 'is_valid_boundary'));
		}
		
		// Category validation
		if ( ! empty($array->category_id)  AND $array->category_id != 0)
		{
			$array->add_rules('category_id', array('Category_Model', 'is_valid_category'));
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
				$array->static_entity_id = 0;
			}
		}

		Kohana::log('debug', 'Static entity: '.$array->static_entity_id);
		
		// Static entity validation
		if ($array->static_entity_id != 0)
		{
			Kohana::log('debug', 'Static entity: '.$array->static_entity_id);

			// Add validation rule for static entity
			$array->add_rules('static_entity_id', array('Static_Entity_Model', 'is_valid_static_entity'));

			// Set
			$array->category_id = 0;
			$array->boundary_id = 0;
		}

		return parent::validate($array, $save);
	}
}
?>
