<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Huduma plugins helper class.
 *
 * @package    Huduma
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */


class huduma_tool_Core {

    /** 
     * Generate Huduma Tab Menus
     */

    public static function huduma_subtabs($this_sub_page = FALSE)
    {   
        $menu = ""; 

        $menu .= ($this_sub_page == "admin_boundaries") ? Kohana::lang('huduma.admin_boundaries') : "<a href=\"".url::site()."admin/huduma\">".Kohana::lang('huduma.admin_boundaries')."</a>";
    
        $menu .= ($this_sub_page == "boundary_types") ? Kohana::lang('huduma.boundary_types') : "<a href=\"".url::site()."admin/huduma/boundary_type\">".Kohana::lang('huduma.boundary_types')."</a>";

        echo $menu;
    
    }   

}

