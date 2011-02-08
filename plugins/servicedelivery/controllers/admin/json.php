<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Json_Controller extends Controller {

    public function __construct()
    {
        parent::__construct();
    }

     public function boundaries($children, $parent_id)
     {
         // Avoid chaching
         header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
         header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
         
         $json = array();

         if ($children == 'children')
         {
             $children = ORM::factory('boundary')->where('parent_id', $parent_id)->find_all();
             
             if (count($children) > 0)
             {
                 foreach ($children as $child)
                 {
                     $json = arr::merge($json, array(
                         $child->id => preg_replace('/\b(\w)/e', 'ucfirst("$1")', strtolower($child->boundary_name))." ".$child->boundary_type->boundary_type_name));
                 }
                 
                 print json_encode(array('parent_id' => $parent_id, 'content'=> $json));
            }
             
         }
         
     }
}
?>
