<?php
/**
 * This file contains functions used to manage Admin Panel
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.0.3
 * @since 1.0.0
 */

/**
 * snipiAdminPanel - Admin Section for Snipi PLugin.
 * This is modified version of nggAdminPanel developed by by Alex Rabe http://alexrabe.boelinger.com/
 * @package wp_snipi
 */
class snipiAdminPanel{

    var $extention;

	/**
	 * Class constructor
	 * @return snipiAdminPanel
	 */
	function snipiAdminPanel() {
		// Add the admin menu
        add_action( 'admin_menu', array (&$this, 'add_menu') );
	}

	/**
	 * Integrates Admin Menu
	 *
	 */
	function add_menu()  {
		add_menu_page( __ngettext( 'Snipi', 'Snipi', 1, 'snipi' ), __ngettext( 'Snipi', 'Snipi', 1, 'snipi' ), 'Snipi overview', SNIPIFOLDER, array (&$this, 'show_menu'));
        add_submenu_page( SNIPIFOLDER , __('Overview', 'snipi'), __('Overview', 'snipi'), 'Snipi overview', SNIPIFOLDER, array (&$this, 'show_menu'));
        add_submenu_page( SNIPIFOLDER , __('About Snipi', 'snipi'), __('About Snipi', 'snipi'), 8, 'snipi-about', array (&$this, 'show_menu'));
	}

	/**
	 * load the script for the defined page and load only this code
	 *
	 */
	function show_menu() {

		global $snipi;

		// init PluginChecker
		$snipiCheck 			= new CheckPlugin();
		$snipiCheck->URL 		= SNIPIURL;
		$snipiCheck->version 	= SNIPIVERSION;
		$snipiCheck->name 	= "snipi";

		// Show update message
		if ( $snipiCheck->startCheck() && (!IS_WPMU) ) {
			echo '<div class="plugin-update">' . __('A new version of NextGEN Gallery is available !', 'nggallery') . ' <a href="http://wordpress.org/extend/plugins/nextgen-gallery/download/" target="_blank">' . __('Download here', 'nggallery') . '</a></div>' ."\n";
		}

	    if (!class_exists('nggLoader')&&$snipi->options['mode']=='ngg') {return;}

  		switch ($_GET['page']){
    		case "snipi-about" :
				include_once ( dirname (__FILE__) . '/about.php' );		    // snipi-about
				wp_snipi_about();
				break;
    		case "wp_snipi_overview":
			default :
				include_once ( dirname (__FILE__) . '/overview.php' ); 	// wp_snipi
				wp_snipi_overview();
				break;
		}
	}
	/**
	 * Read an array from a remote url
	 *
	 * @param string $url
	 * @return array of the content
	 */
	function get_remote_array($url) {
		if ( function_exists(wp_remote_request) ) {

			$options = array();
			$options['headers'] = array(
				'User-Agent' => 'NextGEN Gallery Information Reader V' . NGGVERSION . '; (' . get_bloginfo('url') .')'
			 );

			$response = wp_remote_request($url, $options);

			if ( is_wp_error( $response ) )
				return false;

			if ( 200 != $response['response']['code'] )
				return false;

			$content = unserialize($response['body']);

			if (is_array($content))
				return $content;
		}

		return false;
	}

}
/**
 * WordPress PHP class to check for a new version.
 * @package ngggallery
 * @author Alex Rabe
 * @version 1.50
 *
 // Dashboard update notification example
	function myPlugin_update_dashboard() {
	  $Check = new CheckPlugin();
	  $Check->URL 	= "YOUR URL";
	  $Check->version = "1.00";
	  $Check->name 	= "myPlugin";
	  if ($Check->startCheck()) {
 	    echo '<h3>Update Information</h3>';
	    echo '<p>A new version is available</p>';
	  }
	}

	add_action('activity_box_end', 'myPlugin_update_dashboard', '0');
 *
 */
if ( !class_exists( "CheckPlugin" ) ) {
	class CheckPlugin {
		/**
		 * URL with the version of the plugin
		 * @var string
		 */
		var $URL = 'myURL';
		/**
		 * Version of thsi programm or plugin
		 * @var string
		 */
		var $version = '1.00';
		/**
		 * Name of the plugin (will be used in the options table)
		 * @var string
		 */
		var $name = 'myPlugin';
		/**
		 * Waiting period until the next check in seconds
		 * @var int
		 */
		var $period = 86400;

		/**
		 * check for a new version, returns true if a version is avaiable
		 */
		function startCheck() {

			// If we know that a update exists, don't check it again
			if (get_option( $this->name . '_update_exists' ) == 'true' )
				return true;

			$check_intervall = get_option( $this->name . '_next_update' );

			if ( ($check_intervall < time() ) or (empty($check_intervall)) ) {

				// Do not bother the server to often
				$check_intervall = time() + $this->period;
				update_option( $this->name . '_next_update', $check_intervall );

				if ( function_exists(wp_remote_request) ) {

					$options = array();
					$options['headers'] = array(
						'User-Agent' => 'NextGEN Gallery Version Checker V' . NGGVERSION . '; (' . get_bloginfo('url') .')'
					 );
					$response = wp_remote_request($this->URL, $options);

					if ( is_wp_error( $response ) )
						return false;

					if ( 200 != $response['response']['code'] )
						return false;

					$server_version = unserialize($response['body']);

					if (is_array($server_version)) {
						if ( version_compare($server_version[$this->name], $this->version, '>') ) {
							update_option( $this->name . '_update_exists', 'true' );
							return true;
						}
					}

					delete_option( $this->name . '_update_exists' );
					return false;
				}
			}
		}
	}
}

?>