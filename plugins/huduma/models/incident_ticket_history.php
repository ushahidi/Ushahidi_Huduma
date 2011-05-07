<?php defined('SYSPATH') or die('No direct script access.');

class Incident_Ticket_History_Model extends ORM {
	
	// Table name
	protected $table_name = 'incident_ticket_history';
	
	// Relationships
	protected $belongs_to = array('incident_ticket');
	
	/**
	 * Validates and optionally saves an incident ticket history record from an array
	 * 
	 * @param array $array Values to check
	 * @param boolean $save Save record when validation suceeds
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Setup valiation
		$array = Validation::factory($array)
					->pre_filter('trim')
					->add_rules('incident_ticket_id', 'required', array('Incident_Ticket_Model', 'is_valid_incident_ticket'))
					->add_rules('report_status_id', 'required', array('Report_Status_Model', 'is_valid_report_status'))
					->add_rules('notes', 'required')
					->add_rules('dashboard_user_id', 'required', array('Dashboard_User_Model', 'is_valid_dashboard_user'));
		
		// Pass validation to parent, return
		return parent::validate($array, $save);
	}
}
?>