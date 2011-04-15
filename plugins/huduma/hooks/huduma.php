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

class huduma
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
        plugin::add_stylesheet('huduma/views/css/huduma');
		plugin::add_stylesheet('huduma/views/css/facebox');
		plugin::add_javascript('huduma/views/js/facebox');

        Event::add('ushahidi_action.nav_main_right_tabs', array($this, 'add_huduma_tab'));
		Event::add('ushahidi_action.header_scripts', array($this, '_modify_header_scripts'));

        if (Router::$controller == 'main')
        {
			// Modify the header scripts
			Event::add('ushahidi_action.huduma_overlay_js', array($this, '_overlay_js'));
			
            // Calls the Javascript used function to overlay data on the main map
            Event::add('ushahidi_action.main_map_overlay', array($this, '_overlay_main_map_js_call'));
        }

    }

    /**
     * Adds a service delivery menu to the list of admin menus on the right of the admin console
     */
    public function add_huduma_tab()
    {
        // Get the current event data
        $main_right_tabs = Event::$data;

        // Generate the menu string
        $main_right_tabs = arr::merge($main_right_tabs, array(
            'servicedelivery' => Kohana::lang('ui_huduma.huduma'))
        );
        
        // Set the event data
        Event::$data = $main_right_tabs;
    }


	/**
	 * Renders the javascript for d
	 */
	public function _modify_header_scripts()
	{
		$toggle_button_js = new View('js/toggle_login_button_js');
		$toggle_button_js->render(TRUE);
	}
	
    /**
     * Renders the javascript to render the static entities overlay
     */
    public function _overlay_js()
    {
        // Get the current event data
        $data = Event::$data;

		// Load the JavaScript header for rendering the overlay data
		$overlay_js = new View('js/overlays_js');

		// TODO Check the plugin settings to determine the json URL
		$overlay_js->overlay_json_url = 'overlays/cluster';
		$overlay_js->overlay_layer_name = 'Administrative Units';

		// Append the overlay js to the current data
		$overlay_js->render(TRUE);

    }

    /**
     * Echoes the Javascript function for overlaying data on the main map. This JS function
     * should already have been defined in the javascript header file loaded in this->modify_header_scripts()
     * above
     */
    public function _overlay_main_map_js_call()
    {
        // Call the JS function to render the overlay data
        echo 'overlayMainMap();';
    }

}

// Instantiate the hook
new huduma();