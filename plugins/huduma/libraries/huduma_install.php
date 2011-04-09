<?php defined('SYSPATH') OR die('No direct access allowed.');
/*
 * Installer for the Huduma plugin
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Huduma Plugin Installer
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Huduma_Install {
    private $db;    // Database instance
    private $table_prefix;  // Table prefix

    public function __construct()
    {
        // Create an instance of the database
        $this->db = Database::instance();

        // Get the table prefix for the database
        $this->table_prefix = Kohana::config('database.default.table_prefix');
    }

    /**
     * Creates the database tables for the service delivery plugin
     */
    public function run_install()
    {
        if (FALSE !== $setup_sql_file = Kohana::find_file('sql', 'install_huduma', FALSE, 'sql'))
        {
            // Fetch the contents of the file
            $sql = file_get_contents($setup_sql_file);

            // If a table prefix is specified add it to the SQL
            if ($this->table_prefix)
            {
                $find = array(
                    'CREATE TABLE IF NOT EXISTS `',
                    'INSERT INTO `',
                    'ALTER TABLE `',
                    'UPDATE `'
                );

                $replace = array(
                    'CREATE TABLE IF NOT EXISTS `'.$this->table_prefix.'_',
                    'INSERT INTO `'.$this->table_prefix.'_',
                    'ALTER TABLE `'.$this->table_prefix.'_',
                    'UPDATE `'.$this->table_prefix.'_'
                );

                // Rebuild the SQL
                $sql = str_replace($find, $replace, $sql);
            }

            // Split by ; to get the SQL statement for individual tables
            $queries = explode(';', $sql);

            // Execute the individual CREATE statements
            foreach ($queries as $query)
            {
                $this->db->query($query);
            }

        }
    }

    /**
     * Uinstalls the service delivery plugin. The uninstall SQL script should first
     * backup the tables and their data before dropping them from the schema
     */
    public function uninstall()
    {
        if (FALSE !== $uninstall_sql_file = Kohana::find_file('sql', 'uninstall_huduma.sql', FALSE, 'sql'))
        {
            // Fetch the contents of the file
            $sql = file_get_contents($uninstall_sql_file);

            // If a table prefix has been specified, add it to the SQL
            if ($this->table_prefix)
            {
                // TODO Define the $find and $replace arrays
            }

            // TODO Split by ';' to get the individual SQL statements

            // TODO Execute the individual SQL statements
        }
    }
}
?>
