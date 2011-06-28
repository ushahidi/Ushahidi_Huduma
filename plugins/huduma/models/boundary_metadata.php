<?php
/**
 * Model for the boundary_metadata table
 *
 * @author Ushahidi - http://ushahidi.com
 * @copyright Ushahidi - http://ushahidi.com
 */
class Boundary_Metadata_Model extends ORM {
	
	/**
	 * Database table name
	 * @var string
	 */
	protected $table_name = 'boundary_metadata';
	
	/**
	 * One-to-may relationship definition
	 * @var array
	 */
	protected $has_many = array('boundary_metadata_items');
}
?>