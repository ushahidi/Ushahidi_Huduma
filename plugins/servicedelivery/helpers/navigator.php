<?php defined('SYSPATH') OR die('No direct access allowed.');
/* 
 * Service Delivery Navigation Helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     navigator helper
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class navigator_Core {

    /**
     * Generates the subtabs for the service delivery plugin
     *
     * @param String $this_page
     */
    public static function subtabs($this_sub_page = FALSE)
    {
        // To hold the menus for service delivery
        $menu = "";

        // Service provider list
        $menu .= ($this_sub_page == "serviceproviders")
            ? Kohana::lang('ui_servicedelivery.serviceproviders')
            : "<a href=\"".url::site()."admin/serviceproviders\">".Kohana::lang('ui_servicedelivery.serviceproviders')."</a>";

        // Add/Edit service provider
        $menu .= ($this_sub_page == "createprovider")
            ? Kohana::lang('ui_servicedelivery.add_edit_provider')
            : "<a href=\"".url::site()."admin/serviceproviders/edit\">".Kohana::lang('ui_servicedelivery.add_edit_provider')."</a>";

        // Static Entity list
        $menu .= ($this_sub_page == "entitytypes")
            ? Kohana::lang("ui_servicedelivery.entitytypes")
            : "<a href=\"".url::site()."admin/staticentity\">".Kohana::lang('ui_servicedelivery.entitytypes')."</a>";

        // Add/Edit Static Entities
        $menu .= ($this_sub_page == "entities")
            ? Kohana::lang("ui_servicedelivery.add_edit_entity")
            : "<a href=\"".url::site()."admin/staticentity/edit\">".Kohana::lang('ui_servicedelivery.add_edit_entity')."</a>";

        // Output the menu
        echo $menu;
    }
}
?>
