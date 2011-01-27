<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Service Provider Officers
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Service Provider Officer Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Service_Provider_Officer_Model extends ORM {
    
    // Relationships
    protected $belongs_to = array('service_provider');

    protected $has_many = array('ticket');
    
    // Database table name
    protected $table_name = 'service_provider_officer';

    /**
     * Helper method for fetching tickets for the service provider officer in @param $officer_id
     * which have the status @param $ticket_status
     *
     *
     * @param int $officer_id
     * @param int $ticket_status
     */
    public static function tickets($officer_id, $ticket_status = FALSE)
    {
        // Build the array for the where clause
        $where_array = array('assigned_officer_id'=>$officer_id);
        $where_array = ($ticket_status)
            ? arr::merge($where_array, array('ticket_status'=>$ticket_status))
            : $where_array;

        // Return the tickets for the service provider officer
        return ORM::factory('ticket')
            ->where($where_array)
            ->find_all();

    }
    
}
