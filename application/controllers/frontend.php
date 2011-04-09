<?php
/**
 * This is the controller for the frontend
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Main Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Frontend_Controller extends Template_Controller {

	public $auto_render = TRUE;

    // Main template
	public $template = 'layout';

    // Cache instance
	protected $cache;

	// Cacheable Controller
	public $is_cachable = FALSE;

	// Session instance
	protected $session;

	// Table Prefix
	protected $table_prefix;

	// Themes Helper
	protected $themes;

	// Name of the site
	protected $site_name;

	// Site style
	protected $site_name_style;

	// Use default header view
	protected $use_default_header;

	protected $db;

    public function __construct($use_default_header = TRUE)
    {
        parent::__construct();

		// Set the flag for using the default header
		$this->use_default_header = $use_default_header;
		
        // Load cache
		$this->cache = new Cache;

		$this->db = new Database();
		
		// Load Session
		$this->session = Session::instance();

		$this->template->header  = $this->use_default_header? new View('header') : new View('header_main');

		$this->template->footer  = new View('footer');

		// Themes Helper
		$this->themes = new Themes();
		$this->themes->api_url = Kohana::config('settings.api_url');
		$this->template->header->submit_btn = $this->themes->submit_btn();
		$this->template->header->languages = $this->themes->languages();
		$this->template->header->search = $this->themes->search();

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Retrieve Default Settings
		$this->site_name = Kohana::config('settings.site_name');
        
		// Prevent Site Name From Breaking up if its too long
		// by reducing the size of the font
		if (strlen($this->site_name) > 20)
		{
			$this->site_name_style = " style=\"font-size:21px;\"";
		}
		else
		{
			$this->site_name_style = "";
		}

		$this->template->header->site_name = $this->site_name;
		$this->template->header->site_name_style = $this->site_name_style;
		$this->template->header->site_tagline = Kohana::config('settings.site_tagline');

		$this->template->header->this_page = "";

		if ( ! $this->use_default_header)
		{
			// Themes Helper
			$this->themes = new Themes();
			$this->themes->api_url = Kohana::config('settings.api_url');
			$this->template->header->submit_btn = $this->themes->submit_btn();
			$this->template->header->languages = $this->themes->languages();
			$this->template->header->search = $this->themes->search();

			$this->template->header->site_name = $this->site_name;
			$this->template->header->site_name_style = $this->site_name_style;
			$this->template->header->site_tagline = Kohana::config('settings.site_tagline');
		}
		
		// Check if a dashboard user is logged in
		$is_logged_in = Authlite::instance('authlite')->logged_in();

		if ($is_logged_in)
		{
			// Get the logged in user object
			$logged_in_user = Authlite::instance('authlite')->get_user();

			$this->template->header->logged_in_user = $logged_in_user->name;

			$entity_name = "";

			if ( ! empty($logged_in_user->dashboard_role_id) AND $logged_in_user->dashboard_role_id != 0)
			{
				// Get the item to be displayed under the name of the currently logged in user

				// Load the privileges for the current role
				$privileges = $this->db->where('dashboard_role_id', $logged_in_user->dashboard_role_id)
								->get('dashboard_role_privileges', 1);

				// Role associated with agency
				if ($logged_in_user->dashboard_role->agency_id != 0)
				{
					if ($privileges[0]->static_entity_id != 0)
					{
						// Get the name of the static entity
						$entity_name = Static_Entity_Model::get_entity_name($privileges[0]->static_entity_id);
					}
					else
					{
						// Get agency name
						$entity_name = ORM::factory('agency', $logged_in_user->dashboard_role->agency_id)->agency_name;

						// Check for agency access within boundary
						if ($privileges[0]->boundary_id != 0)
						{
							// Get the boundary name including its type
							$boundary = ORM::factory('boundary', $privileges[0]->boundary_id);

							// Set the entity name
							$entity_name .= ','.$boundary->boundary_name.' '.$boundary->boundary_type->boundary_type_name;
						}
					}

				}
				else
				{
					// Role not assoiciated with agency
					//
					// Check for static entity privilege
					if ($privileges[0]->static_entity_id != 0)
					{
						$entity_name = Static_Entity_Model::get_entity_name($privileges[0]->static_entity_id);
					}
					elseif ($privileges[0]->boundary_id != 0)
					{
						// Get the boundary name including its type
						$boundary = ORM::factory('boundary', $privileges[0]->boundary_id);

						// Set the entity name
						$entity_name .= ','.$boundary->boundary_name.' '.$boundary->boundary_type->boundary_type_name;
					}
				}
			}

			// Set value for the entity on the UI
			$this->template->header->static_entity_name = $entity_name;
		}

		$this->template->header->is_logged_in = $is_logged_in;
		
		// Google Analytics
		$google_analytics = Kohana::config('settings.google_analytics');
		$this->template->footer->google_analytics = $this->themes->google_analytics($google_analytics);

        // Load profiler
        // $profiler = new Profiler;

		// Get tracking javascript for stats
		$this->template->footer->ushahidi_stats = (Kohana::config('settings.allow_stat_sharing') == 1)
                ? Stats_Model::get_javascript()
                : '';

		// Add copyright info
		$this->template->footer->site_copyright_statement = '';
		$site_copyright_statement = trim(Kohana::config('settings.site_copyright_statement'));
		
        if ($site_copyright_statement != '')
		{
			$this->template->footer->site_copyright_statement = $site_copyright_statement;
		}
    }

    /**
     * Retrieves the list of categories
     * 
     * @param array $selected_categories
     * @return array
     */
    protected function get_categories($selected_categories)
    {
        $categories = ORM::factory('category')
                        ->where('category_visible', '1')
                        ->where('parent_id', '0')
                        ->where('category_trusted != 1')
                        ->orderby('category_title', 'ASC')
                        ->find_all();

        return $categories;
    }

}
