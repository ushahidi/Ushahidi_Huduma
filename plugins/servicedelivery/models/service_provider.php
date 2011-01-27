<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Service Providers
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Service Provider Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Service_Provider_Model extends ORM {
    
    // Database table name
    protected $table_name = 'service_provider';
    
    // Relationships
    protected $has_many = array('service_provider_officer');
    
    protected $belongs_to = array('boundary');
    
    /**
     * Helper method for fetching the tickets with status @param $ticket_status for the service provider specified in @param $service_provider_id
     *
     * @param int $service_provider_id
     * @param int $ticket_status
     */
    public static function tickets($service_provider_id, $ticket_status = FALSE)
    {
        // Build the array for the where clause
        $where_array = array('service_provider_id'=>$service_provider_id);
        $where_array = ($ticket_status)
            ? arr::merge($where_array, array('ticket_status'=>$ticket_status))
            : $where_array;

        // Return the tickets for the service provider
        return ORM::factory('ticket')
            ->where($where_array)
            ->find_all();
    }

    /**
     * Returns an array containing the list of service providers excluding the
     * service provider specified in @param $service_provider_id
     *
     * @param int $service_provider_id
     */
    public static function service_providers($service_provider_id = NULL)
    {
        // Fetch the service providers
        $providers = ORM::factory('service_provider')
                        ->where(($service_provider_id != NULL)
                                ? array('id !=' => $service_provider_id)
                                : array('id >' => 0))
                        ->find_all();

        // To hold the return data
        $items = array();

        // Iterate over the result set and return an array
        foreach ($providers as $provider)
        {
            $items[$provider->id]['service_provider_id'] = $provider->id;
            $items[$provider->id]['provider_name'] = $provider->provider_name;
            $items[$provider->id]['category_id'] = $provider->category_id;
            $items[$provider->id]['administrative_boundary_id'] = $provider->administrative_boundary_id;
        }

        return $items;
    }
}
