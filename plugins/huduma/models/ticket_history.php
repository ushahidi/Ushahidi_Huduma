<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Ticket History
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Ticket History Model
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Ticket_History_Model extends ORM {
    // Relationships
    protected $belongs_to = array('ticket', 'agency_staff');
    
    // Database table name
    protected $table_name = 'ticket_history';
}
