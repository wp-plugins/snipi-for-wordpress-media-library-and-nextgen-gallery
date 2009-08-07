<?php
/*
Plugin Name: Snipi for Wordpress
Plugin URI: http://snipi.com/tour/view/social/
Description: Drag and drop images into the Snipi Toolbar and they automatically appear in your WordPress Media Library or NextGen Gallery. <a href="admin.php?page=snipi-for-wordpress-media-library-and-nextgen-gallery">Configure</a>.  Get the toolbar (<a href="http://www.snipi.com">www.snipi.com</a>).
Author: Snipi, Inc <info@snipi.com>
Version: 1.2.0
Author URI: http://snipi.com/tour/view/social/
*/
/**
 * Plugin to upload images from Snipi.com to WordPress gallery
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 */
error_reporting(E_ERROR | E_PARSE);
/** load Snipi core classes and functions*/
require_once 'core.php';
/* show errors*/
if (class_exists('snipiLoader')) {
	// Start plugin
	global $snipi;
	$snipi = new snipiLoader();
}
?>