<?php
/**
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 */
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die (_e('You are not allowed to call this page directly.', 'snipi'));
}
/**
 * creates all tables for the gallery
 * called during register_activation hook
 *
 * @access private
 * @return void
 */
function snipi_install ()
{
	global $snipi;
	// Check for capability
	if (! current_user_can('activate_plugins'))
		return;


	// Set the capabilities for the administrator
	$role = get_role('administrator');
	// We need this role, no other chance
	if (empty($role)) {
		update_option("snipi_init_check", __('Sorry, Snipi works only with a role called administrator', "snipi"));
		return;
	}

	//check that we have NextGen Gallery installed
	if (! class_exists('snipiLoader') && $snipi->options['mode'] == 'ngg') {
		return;
	}

	$role->add_cap('snipi-overview');
	$role->add_cap('snipi-about');

	// upgrade function changed in WordPress 2.3
	//require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	$options = get_option('snipi_options');
	// set the default settings, if we didn't upgrade
	if (empty($options))
		snipi_default_options();
}
/**
 * Setup the default option array for the gallery
 *
 * @access private
 * @since 1.0.0
 * @return void
 */
function snipi_default_options ()
{
	global $blog_id;
	$snipi_options['gallerypath'] = 'wp-content/snipi/'; // set default path to the gallery
	$snipi_options['mode'] = 'wp'; // set default path to the gallery
	$snipi_options['api'] = wp_snipi_get_api(); // set default path to the gallery
	// special overrides for WPMU
	if (IS_WPMU) {
		// get the site options
		$snipi_wpmu_options = get_site_option('snipi_options');
		// get the default value during installation
		if (! is_array($snipi_wpmu_options)) {
			$snipi_wpmu_options['gallerypath'] = 'wp-content/blogs.dir/%BLOG_ID%/files/';
			$snipi_wpmu_options['api'] = wp_snipi_get_api();
			update_site_option('snipi_options', $snipi_wpmu_options);
		}
		$snipi_options['gallerypath'] = str_replace("%BLOG_ID%", $blog_id, $snipi_wpmu_options['gallerypath']);
		$snipi_options['api'] = $snipi_wpmu_options['api'];
	}
	update_option('snipi_options', $snipi_options);
}
/**
 * Deregister a capability from all specified roles
 *
 * @access private
 * @param string $capability name of the capability which should be deregister
 * @param array $roles
 * @return void
 */
function wp_snipi_remove_capability ($capability, $roles)
{
	if (! empty($roles)) {
		foreach ($roles as $role) {
			$role = get_role($role);
			$role->remove_cap($capability);
		}
	}
}
/**
 * Register a capability from all specified roles
 *
 * @access private
 * @param string $capability name of the capability which should be register
 * @param array $roles
 */
function wp_snipi_add_capability ($capability, $roles)
{
	if (! empty($roles)) {
		foreach ($roles as $role) {
			$role = get_role($role);
			$role->add_cap($capability);
		}
	}
}
/**
 * Uninstall all settings and tables
 * Called via Setup and register_unstall hook
 *
 * @access private
 * @return void
 */
function snipi_uninstall ()
{
	//@todo remove api and url from snipi database
	// then remove all options
	delete_option('snipi_options');
	$roles = array("subscriber" , "contributor" , "author" , "editor" , "administrator");
	// now remove the capability
	wp_snipi_remove_capability("snipi-overview", $roles);
	wp_snipi_remove_capability("snipi-about", $roles);
}
?>