<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller for the publicly accessible dashboards
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Dashboards Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class Dashboards_Controller extends Frontend_Controller {
	
	// No. of report items to be displayed in each of the stacked views
	private $report_items_per_view = 3;
	
	public function index()
	{
		// Prevent invalid access
		if ( ! $this->_is_valid_request($_GET))
		{
			Kohana::log('error', 'Invalid dashboard parameters');
			url::redirect('main');
		}
		
		if ($_GET['action'] == 'list' OR ($_GET['action'] == 'filter' AND ! empty($_GET['page'])))
		{
			// Get the dashboard type
			$dashboard_type = $_GET['type'];
			switch ($dashboard_type)
			{
				case 'category':
					$this->_show_category_dashboard($_GET['id']);
				break;
			
				default:
				Kohana::log('error', sprintf('Unknown dashboard type: %s', $dashboard_type));
				url::redirect('main');
			}
		}
		elseif ($_GET['action'] == 'filter')
		{
			$this->template = "";
			$this->auto_render = FALSE;
			
			// Fetch the category ID
			$category_id  = $_GET['id'];
			
			// Set up pagination
			$pagination = navigator::get_category_reports_paginator($category_id, $_GET['filter']);
			
			// To hold the reports view
			$reports = "";
			
			// Determine filter to use
			switch($_GET['filter'])
			{
				case 'all':
					$reports = Category_Model::get_category_reports($category_id, 'all', $pagination->sql_offset, 
						$this->report_items_per_view);
				break;
				
				case 'resolved':
					$reports = Category_Model::get_category_reports($category_id, 'resolved', $pagination->sql_offset, 
						$this->report_items_per_view);
				break;
				
				case 'unresolved':
					$reports = Category_Model::get_category_reports($category_id, 'unresolved', $pagination->sql_offset, 
						$this->report_items_per_view);
				break;
				
				default:
					// Invalid filter -  show all
					$reports = Category_Model::get_category_reports($category_id, 'all', $pagination->sql_offset, 
						$this->report_items_per_view);
			}
			// Print the returned content
			print ($reports->count() > 0)
				? navigator::get_reports_view($reports, 'reports/view/', $pagination) 
				: "";
		}
	}
	
	/**
	 * Helper function to check if the dashboard request is valid
	 *
	 * @param array $request_data HTTP GET/POST data to be verified
	 * @return boolean
	 */
	private function _is_valid_request(array & $request_data)
	{
		// Check for action
		if (empty($request_data['action']) OR ! isset($request_data['action']))
		{
			return FALSE;
		}
		
		if (empty($request_data['type']) OR ! isset($request_data['type']))
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Loads the category dashboard
	 */
	private function _show_category_dashboard($category_id)
	{
		if ( ! Category_Model::is_valid_category($category_id))
		{
			url::redirect('main');
		}
		else
		{
			$this->template->content = new View('huduma/category_dashboard');
			
			// Get the category stats
			$category_stats = navigator::get_category_stats($category_id);
			$stats_record = $category_stats[0];
			$stats = array(
				'total_reports' => $stats_record->total_reports,
				'resolved' => ($stats_record->resolved > 0)? round(($stats_record->resolved/$stats_record->total_reports) * 100, 2) : 0,
				'unresolved' => ($stats_record->unresolved > 0)? round(($stats_record->unresolved/$stats_record->total_reports) * 100, 2) : 0,
				'unassigned' => $stats_record->total_reports - ($stats_record->resolved + $stats_record->unresolved)
			);
			
			$this->template->content->category_stats = $stats;
			$pagination = navigator::get_category_reports_paginator($category_id);
			$this->template->content->category = ORM::factory('category', $category_id);
			// Get the reports for the category
			$reports = Category_Model::get_category_reports($category_id, 'all', $pagination->sql_offset, $this->report_items_per_view);
			$this->template->content->category_reports_view = navigator::get_reports_view($reports, 'reports/view/', $pagination);
			
			// Javascript
			$this->themes->js = new View('js/category_dashboard_js');
			
			// Header block
			$this->template->header->header_block = $this->themes->header_block();
		}
	}
		
}
