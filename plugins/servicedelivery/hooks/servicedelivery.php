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
        // Set Table Prefix
        $this->table_prefix = Kohana::config('database.default.table_prefix');

        Event::add('system.pre_controller', array($this, 'add'));
    }

    /**
     * Adds all the events to the Ushahidi main application
     */
    public function add()
    {
        plugin::add_stylesheet('servicedelivery/views/css/servicedelivery');
		plugin::add_stylesheet('servicedelivery/views/css/facebox');
		plugin::add_javascript('servicedelivery/views/js/facebox');
		
        Event::add('ushahidi_action.nav_main_right_tabs', array($this, 'generate_service_delivery_tab'));

        if (Router::$controller == 'main')
        {
            // Modify the header scripts
            Event::add('ushahidi_action.header_scripts', array($this, 'modify_header_scripts'));

            // Calls the Javascript used function to overlay data on the main map
            Event::add('ushahidi_action.main_map_overlay', array($this, 'overlay_main_map'));
        }

    }

    /**
     * Adds a service delivery menu to the list of admin menus on the right of the admin console
     */
    public function generate_service_delivery_tab()
    {
        // Get the current event data
        $main_right_tabs = Event::$data;

        // Generate the menu string
        $main_right_tabs = arr::merge($main_right_tabs, array(
            'servicedelivery' => Kohana::lang('ui_servicedelivery.servicedelivery'))
        );
        
        // Set the event data
        Event::$data = $main_right_tabs;
    }


    /**
     * Modifies the header scripts
     */
    public function modify_header_scripts()
    {
        // Get the current event data
        $data = Event::$data;

        // Load the JavaScript header for rendering the overlay data
        $overlay_js = new View('js/overlays_js');

        // TODO Check the plugin settings to determine the json URL
        $overlay_js->overlay_json_url = 'overlays/cluster';
        $overlay_js->overlay_layer_name = 'Administrative Units';

        // Set the event data
        Event::$data .= $overlay_js;
    }

    /**
     * Echoes the Javascript function for overlaying data on the main map. This JS function
     * should already have been defined in the javascript header file loaded in this->modify_header_scripts()
     * above
     */
    public function overlay_main_map()
    {
        // Call the JS function to render the overlay data
        echo 'overlayMainMap();';
    }

}

// Instantiate the hook
new servicedelivery();