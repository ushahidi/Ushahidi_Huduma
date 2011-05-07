<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for the incident_ticket table
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Incident Ticket Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Incident_Ticket_Model extends ORM
{
	// Table name
	protected $table_name = 'incident_ticket';
	
	// Relationships
	protected $has_many = array('incident_ticket_history');
	protected $belongs_to = array('report_status', 'report_priority');
	
	/**
	 * Validates and optionally saves a new incident ticket record from an array
	 *
	 * @param array $array Values to check
	 * @param boolean $save Save record when validation succeeds
	 * @return boolean
	 */
	public function validate(array & $array, $save = FALSE)
	{
		// Setup validation
		$array = Validation::factory($array)
					->pre_filter('trim')
					->add_rules('incident_id', 'required', array('Incident_Model', 'is_valid_incident'))
					->add_rules('report_status_id', 'required', array('Report_Status_Model', 'is_valid_report_status'))
					->add_rules('report_priority_id', 'required', array('Report_Priority_Model', 'is_valid_report_priority'));

		// Validate agency
		if ( ! empty($array->agency_id) AND $array->agency_id != 0)
		{
			$array->add_rules('agency_id', array('Agency_Model', 'is_valid_agency'));
		}
		
		// Validate static entity id
		if ( ! empty($array->static_entity_id))
		{
			$array->add_rules('static_entity_id', array('Static_Entity_Model', 'is_valid_static_entity'));
			// TODO: Review this - should a service agency be actioned on reports that are for a specific
			// static entity?
			// $array->agency_id = "";
		}
		
		// Pass validation to parent
		return parent::validate($array, $save);
	}
	
	/**
	 * Helper function to check if a ticket id is valid and exists in the database
	 *
	 * @param int $ticket_id
	 * @return boolean
	 */
	public static function is_valid_incident_ticket($ticket_id)
	{
		return (preg_match('/^[1-9](\d*)$/', $ticket_id) > 0)
			? self::factory('incident_ticket', $ticket_id)->loaded
			: FALSE;
	}
	
	/**
	 * Returns an instance of Incident_Ticket_Model provided an incident id
	 *
	 * @param int $incident_id
	 */
	public function get_incident_ticket($incident_id)
	{
		if ( ! Incident_Model::is_valid_incident($incident_id))
		{
			return FALSE;
		}
		else
		{
			// Get tickets for the specified incident id
			$tickets = self::factory('incident_ticket')->where('incident_id', $incident_id)
						->orderby('id', 'asc')
						->find_all();
			
			// The query should only return one ticket per incident
			if ($tickets->count() == 1)
			{
				// Return instance
				$items = $tickets->as_array();
				return $items[0];
			}
			else
			{
				return FALSE;
			}
		}
	}
	
	/**
	 * Closes and save an existing incident ticket - sets the report_status_id = 2
	 */
	public function close()
	{
		// Fetch the creation date to prevent overwriting by the DB engine upon save
		$creation_date = $this->created_on;
		
		$this->report_status_id = 2;
		$this->created_on = $creation_date;
		$this->save();
	}
}
?>