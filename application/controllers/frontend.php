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
	protected $use_default_header = FALSE;

    public function __construct()
    {
        parent::__construct();

        // Load cache
		$this->cache = new Cache;

		// Load Session
		$this->session = Session::instance();

		$this->template->header  = new View('header');
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

}
