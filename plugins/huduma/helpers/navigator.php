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
            ? Kohana::lang('ui_huduma.boundaries')
            : "<a href=\"".url::site()."admin/servicedelivery\">".Kohana::lang('ui_huduma.boundaries')."</a>";

        // Service Providers
        $menu .= ($this_sub_page == "agencies")
            ? Kohana::lang('ui_huduma.agencies')
            : "<a href=\"".url::site()."admin/agencies\">".Kohana::lang('ui_huduma.agencies')."</a>";


        // Static Entities
        $menu .= ($this_sub_page == "entities")
            ? Kohana::lang("ui_huduma.entities")
            : "<a href=\"".url::site()."admin/entities\">".Kohana::lang('ui_huduma.entities')."</a>";

        // Dashboard users
        $menu .= ($this_sub_page == "dashboard_users")
            ? Kohana::lang("ui_huduma.dashboard_users")
            : "<a href=\"".url::site()."admin/dashboard/users\">".Kohana::lang('ui_huduma.dashboard_users')."</a>";
		
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

    public static function child_boundaries($boundary_id, $recursive = FALSE)
    {
        // To hold the hierarchy
        $hierarchy = '';

        // Get the child boundaries
        $children  = ORM::factory('boundary')->where('parent_id', $boundary_id)->find_all();
        if (count($children) > 0)
        {
            $hierarchy .= "<ul>";
            foreach ($children as $child)
            {
                $parent_css_id = 'boundary_'.$child->id;
                $hierarchy .= "<li>";
                $hierarchy .= "<a href=\"javascript:showHierarchy(".$child->id.", '".$parent_css_id."')\">".$child->boundary_name." ".$child->boundary_type->boundary_type_name."</a>";
                $hierarchy .= "<div id=\"".$parent_css_id."\" style=\"visibility:hidden;\"></div>";
                
                // Check if the boundaries are to be recursively fetched
                if ($recursive)
                {
                    $hierarchy .= self::child_boundaries($child->id);
                }

                $hierarchy .= "</li>";
            }
            $hierarchy .= "</ul>";
        }
        return $hierarchy;
    }
    
    /**
     * Helper function to recursively fetch and build hierarchical HTML for the inline comments 
     * associated with the comment specified in @param $comment_id.
     * 
     * @param   int $comment_id
     * @return  string
     */
    public static function inline_comments($comment_id)
    {
        // To hold the comment tree
        $comment_tree = '';
        
        // Fetch the inline commnets
        $children = Static_Entity_Comment_Model::get_inline_comments($comment_id);
        
        Kohana::log('info', sprintf('Fetch %d inline comments for comment %d', $children->count(), $comment_id));
        
        if ($children->count() > 0)
        {
            $comment_tree .= "<ul class=\"children inlinecomments\">";
            
            foreach ($children as $comment)
            {
                $comment_tree .= "<li class=\"inlinecomment\" style=\"clear: both;\"  id=\"dashboard_comment_".$comment->id."\">";
                $comment_tree .= "  <div class=\"dashboard_comment_block\">";
                
                // Comment author, date and time
                $comment_tree .= "      <div>";
                $comment_tree .= "          <strong>".$comment->comment_author."</strong>&nbsp;";
                $comment_tree .= "          <span class=\"comment_date_time\">";
                $comment_tree .= "          ".date('g:m a', strtotime($comment->comment_date))."&nbsp;on&nbsp;";
                $comment_tree .= "          ".date('F j, Y', strtotime($comment->comment_date));
                $comment_tree .= "          </span>";
                $comment_tree .= "      </div>";
                
                // Comment description
                $comment_tree .= "      <div class=\"dashboard_comment_text\">";
                $comment_tree .= "          <p>".$comment->comment_description."</p>";
                $comment_tree .= "      </div>";
                
                $comment_tree .= "  </div>";
                
                // Comment actions panel
                $comment_tree .= "<span class=\"dashboard_comment_actions\">";
                $comment_tree .="   <a href=\"#".Kohana::lang('ui_main.reply')."\" onclick=\"showCommentBox('dashboard_comment_".$comment->id."', '".urlencode($comment->id)."')\">";
                $comment_tree .= Kohana::lang('ui_main.reply');
                $comment_tree .= "  </a>";
                $comment_tree .= "  <a href=\"#".Kohana::lang('ui_main.share')."\">";
                $comment_tree .= "      ".Kohana::lang('ui_main.share');
                $comment_tree .= "  </a>";
                $comment_tree .= "</span>";
                
                // Recursion - Fetch the inline comments for this comment
                $comment_tree .= self::inline_comments($comment->id);
                
                // Comment box/form holder
                $comment_tree .= "<div class=\"comment_box_holder\"></div>";
                
                // Close the parent comment
                $comment_tree .= "</li>";
            }
            
            $comment_tree .= "</ul>";
        }
        
        return $comment_tree;
    }
}
?>
