<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Service delivery plugin hook
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Service Delivery plugin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class servicedelivery
{
    /**
     * Registers the main event method - add
     */
    public function __construct()
    {
        Event::add('system.pre_controller', array($this, 'add'));
    }

    /**
     * Adds all the events to the Ushahidi main application
     */
    public function add()
    {
        Event::add('ushahidi_action.nav_main_right_tabs', array($this, 'generate_service_delivery_tab'));

    }

    /**
     * Adds a service delivery menu to the list of admin menus on the right of the admin console
     */
    public function generate_service_delivery_tab()
    {
        // Get the current event data
        $main_right_tabs = Event::$data;

        // Generate the menu string
        $main_right_tabs = arr::merge($main_right_tabs, array('boundaries' => Kohana::lang('ui_servicedelivery.servicedelivery')));
        
        // Set the event data
        Event::$data = $main_right_tabs;
    }

}

new servicedelivery();