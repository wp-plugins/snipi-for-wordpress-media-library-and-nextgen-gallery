<?php
/**
 * This file contains functions and includes files required for url image upload using NEXTGEN Gallery Plugin
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.1.1
 * @since 1.0.0
 *
 * @param api User Api(required)
 * @param img_url Image url (required)
 */
global $wpdb;
/** Set Root directory for NextGen Gallery plugin */
define('NEXTGEN_ROOT', dirname(dirname(dirname(__FILE__))) . "/nextgen-gallery/");
$res = array('success' => false);
// Load required files and set some useful variables
require_once (NEXTGEN_ROOT . 'ngg-config.php');
include_once (NEXTGEN_ROOT . 'admin/functions.php'); // admin functions
include_once (NEXTGEN_ROOT . 'admin/addgallery.php'); // nggallery_admin_add_gallery
// reference thumbnail class
include_once (nggGallery::graphic_library());
include_once (NEXTGEN_ROOT . 'lib/core.php');
// get the plugin options
$ngg_options = get_option('ngg_options');
//get user info
//filter request variables
$api = (isset($_GET['api'])) ? strip_tags($_GET['api']) : '';
$img_url = (isset($_GET['img_url']) && strlen($_GET['img_url'])) ? urldecode($_GET['img_url']) : '';
//validate API
$user = wp_snipi_get_user($api);
if (! count($user)) {
    $res['errors'][] = 'Error, API is not valid: ' . $api;
}


//validate external image
if (!isAllowedUrl($img_url)){
    $res['errors'][] = "The file from $img_url could not be uploaded.";
}

// WPMU action
if (nggAdmin::check_quota()) {
    $res['errors'][] = 'No space';
}
if (! (isset($res['errors']) && count($res['errors']))) {
    $user_id = $user->ID;
    //get gallery with name snipi
    $gallery = wp_snipi_get_gallery(SNIPI_GALLERY_TITLE, $user_id);
    if (! $gallery) {
        $res['errors'][] = 'Failure in database, no gallery with title' . SNIPI_GALLERY_TITLE;
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
    //set gallery id
    $galleryID = $gallery->gid;
    // Images must be an array
    $imageslist = array();
    // get the path to the gallery
    $gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->nggallery WHERE gid = '$galleryID' ");
    if (! $gallerypath) {
        $res['errors'][] = 'Failure in database, no gallery path set !';
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
    // read list of images
    $dirlist = nggAdmin::scandir(WINABSPATH . $gallerypath);
    $filepart = pathinfo(strtolower($img_url));
    // required until PHP 5.2.0
    $filepart['filename'] = substr($filepart["basename"], 0, strlen($filepart["basename"]) - (strlen($filepart["extension"]) + 1));
    $filename = sanitize_title($filepart['filename']) . '.' . $filepart['extension'];
    // check if this filename already exist in the folder
    $i = 0;
    while (in_array($filename, $dirlist)) {
        $filename = sanitize_title($filepart['filename']) . '_' . $i ++ . '.' . $filepart['extension'];
    }
    $dest_file = WINABSPATH . $gallerypath . '/' . $filename;
    //check for folder permission
    if (! is_writeable(WINABSPATH . $gallerypath)) {
        $res['errors'][] = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'nggallery'), WINABSPATH . $gallerypath);
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
        //get file from snipi.com
    if (! LoadImageCURL($img_url, $dest_file)) {
        $res['errors'][] = 'Error, the file could not be moved to : ' . $dest_file;
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
    //get metadata for uploaded file
    $img = getimagesize($dest_file);
    //verify that uploaded file has allowed mime type
    if (! ($img && preg_match('/^image\/(' . SNIPI_ALLOWED_IMAGE_EXT . ')$/', $img['mime']))) {
        unlink($dest_file);
        $res['errors'][] = 'Error, the file could not be moved to : ' . $dest_file;
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
    
    if (! nggAdmin::chmod($dest_file)) {
        $res['errors'][] = 'Error, the file permissions could not be set';
        if (function_exists('json_encode'))
            echo json_encode($res);
        else {
            require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
            $json = new Services_JSON();
            echo $json->encode($res);
        }
        exit();
    }
    // add to imagelist & dirlist
    $imageslist[] = $filename;
    $dirlist[] = $filename;
    if (count($imageslist) > 0) {
        // add images to database
        $image_ids = nggAdmin::add_Images($galleryID, $imageslist);
        //create thumbnails
        $result = nggAdmin::create_thumbnail($image_ids[0]);
        //add the preview image if needed
        nggAdmin::set_gallery_preview($galleryID);
        $res['success'] = true;
    }
}
if (function_exists('json_encode'))
    echo json_encode($res);
else {
    require_once (dirname(dirname(__FILE__)) . '/lib/json.php');
    $json = new Services_JSON();
    echo $json->encode($res);
}
?>