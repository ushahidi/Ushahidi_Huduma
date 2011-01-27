<?php
/**
 * Performs install/uninstall methods for Huduma plugins
 *
 * @package    Ushahidi
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class Huduma_Install {

	/**
	 * Constructor to load the shared database library
	 */
	public function __construct()
	{
		$this->db =  new Database();
	}

	/**
	 * Creates the required database tables for the huduma module
	 */
	public function run_install()
	{
		// Create the database tables
		// Include the table_prefix
		$this->db->query("
            CREATE TABLE IF NOT EXISTS`".Kohana::config('database.default.table_prefix')."boundary`
            (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `boundary_name` varchar(100) NOT NULL COMMENT 'name of the boundary',
				`boundary_type_id` INT(4) NOT NULL COMMENT 'boundary_type_id of the boundary',
                 PRIMARY KEY (id)
            );");
		$this->db->query("
            CREATE TABLE IF NOT EXISTS`".Kohana::config('database.default.table_prefix')."boundary_type`
            (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`parent_id` INT(4) NOT NULL COMMENT 'parent_id of the boundary',
                `boundary_type_name` varchar(100) NOT NULL COMMENT 'name of the boundary type',
                 PRIMARY KEY (id)
            );");

	}

	/**
	 * Deletes the database tables for the huduma module
	 */
	public function uninstall()
	{
		$this->db->query("DROP TABLE".Kohana::config('database.default.table_prefix')."boundary;");
		$this->db->query("DROPTABLE".Kohana::config('database.default.table_prefix')."boundary_type;");
	}
}
