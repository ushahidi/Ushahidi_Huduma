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

        // Administrative boundaries
        $menu .= ($this_sub_page == "boundaries")
            ? Kohana::lang('ui_servicedelivery.boundaries')
            : "<a href=\"".url::site()."admin/servicedelivery\">".Kohana::lang('ui_servicedelivery.boundaries')."</a>";

        // Service Providers
        $menu .= ($this_sub_page == "agencies")
            ? Kohana::lang('ui_servicedelivery.agencies')
            : "<a href=\"".url::site()."admin/agencies\">".Kohana::lang('ui_servicedelivery.agencies')."</a>";


        // Static Entities
        $menu .= ($this_sub_page == "entities")
            ? Kohana::lang("ui_servicedelivery.entities")
            : "<a href=\"".url::site()."admin/entities\">".Kohana::lang('ui_servicedelivery.entities')."</a>";

        // Output the menu
        echo $menu;
    }

    /**
     * Generates the breadcrumb for the boundary specified in @param $boundary_id
     *
     * @param int $boundary_id
     */
    public static function boundary_breadcrumb($boundary_id)
    {
        // Get the boundary name and parent id
        $boundary = ORM::factory('boundary', $boundary_id);

        // Get the boundary
        $breadcrumb = "";

        // Get the boundary type name
        $boundary_type_name = $boundary->boundary_type->boundary_type_name;
        
        if ($boundary->parent_id > 0)
        {
            $breadcrumb = $boundary->boundary_name." ".$boundary_type_name;
            $breadcrumb = self::boundary_breadcrumb($boundary->parent_id)."&rarr;".$breadcrumb;
        }
        else
        {
            $breadcrumb = $boundary->boundary_name." ".$boundary_type_name.$breadcrumb;
        }
        return $breadcrumb;
    }
}
?>
