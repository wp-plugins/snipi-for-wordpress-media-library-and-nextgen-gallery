<?php
/**
 * This file contains functions used to manage Admin Panel
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 */
/**
 * snipiAdminPanel - Admin Section for Snipi PLugin.
 * This is modified version of nggAdminPanel developed by by Alex Rabe http://alexrabe.boelinger.com/
 * @package wp_snipi
 */
class snipiAdminPanel
{
	/**
	 * Class constructor
	 * @return snipiAdminPanel
	 */
	function snipiAdminPanel ()
	{
		// Add the admin menu
		add_action('admin_menu', array(&$this , 'add_menu'));
	}
	/**
	 * Integrates Admin Menu
	 *
	 */
	function add_menu ()
	{
		add_menu_page(__('Snipi', 'snipi'), __('Snipi', 'snipi'), 'snipi-overview', SNIPIFOLDER, array(&$this , 'show_menu'));
		add_submenu_page(SNIPIFOLDER, __('Overview', 'snipi'), __('Overview', 'snipi'), 'snipi-overview', SNIPIFOLDER, array(&$this , 'show_menu'));
		add_submenu_page(SNIPIFOLDER, __('About Snipi', 'snipi'), __('About Snipi', 'snipi'), 8, 'snipi-about', array(&$this , 'show_menu'));
	}
	/**
	 * load the script for the defined page and load only this code
	 *
	 */
	function show_menu ()
	{
		switch ($_GET['page']) {
			case "snipi-about":
				include_once (dirname(__FILE__) . '/about.php'); // snipi-about
				wp_snipi_about();
				break;
			default:
				include_once (dirname(__FILE__) . '/overview.php'); // wp_snipi
				wp_snipi_overview();
				break;
		}
	}
}
?>