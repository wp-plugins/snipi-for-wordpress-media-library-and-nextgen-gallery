<?php
/*
Plugin Name: Snipi for Wordpress
Plugin URI: http://snipi.com/tour/view/social/
Description: Drag and drop images into the Snipi Toolbar and they automatically appear in your WordPress Media Library or NextGen Gallery. <a href="admin.php?page=snipi-for-wordpress-media-library-and-nextgen-gallery">Configure</a>.  Get the toolbar (<a href="http://www.snipi.com">www.snipi.com</a>).
Author: Snipi, Inc <info@snipi.com>
Version: 1.1.0
Author URI: http://snipi.com/tour/view/social/
*/
/**
 * Plugin to upload images from Snipi.com to WordPress gallery
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.1.0
 */
//only allow to call this page directly when image url and api specified
if (preg_match('#'.basename(__FILE__).'#',$_SERVER['PHP_SELF'])&&(!isset($_GET['api'])||!isset($_GET['img_url']))){
    die('You are not allowed to call this page directly.');
}
if (!function_exists('add_action')){
    define('SNIPI_API',true);
    /** load WordPress settings and functions. Required for Front End*/
    require_once ("../../../wp-config.php");

}

/** load Snipi core classes and functions*/
require_once 'core.php';
/* show errors*/
if (class_exists('snipiLoader')){
    // Start plugin
    global $snipi;
    $snipi=new snipiLoader();
}
?>