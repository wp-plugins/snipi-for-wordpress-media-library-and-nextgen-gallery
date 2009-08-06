<?php
/**
 * This file contains functions and includes files required for url image upload using NEXTGEN Gallery Plugin
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 *
 * @param api User Api(required)
 * @param img_url Image url (required)
 */
global $ngg;
global $wpdb;
global $wp_taxonomies;
// get the plugin options
$ngg_options = get_option('ngg_options');
$wp_snipi_options = get_option('snipi_options');
$json = array('success' => false);
$data_keys = array('api' , 'img_url' , 'title' , 'description' , 'list' , 'tags');
$data = $post_data = array();
$wp_snipi_data = (isset($_GET['wp_snipi_data']) && strlen($_GET['wp_snipi_data'])) ? $_GET['wp_snipi_data'] : '';
if (! strlen($wp_snipi_data)) {
	$json['errors'][] = 'Missing parameter';
}
$json_input = base64_decode($wp_snipi_data);
$json_data = (array) wp_snipi_json_decode($json_input);
foreach ($data_keys as $key) {
	if (in_array($key, array_keys($json_data))) {
		$data[$key] = $json_data[$key];
	} else {
		$json['errors'][] = 'Missing parameter ' . $key;
	}
}
if ($data['api'] != $wp_snipi_options['api']) {
	$json['errors'][] = 'Error, API is not valid: ' . $data['api'] . ' !=' . $wp_snipi_options['api'];
}
if (strlen($data['title'])) {
	$post_data['post_title'] = $data['title'];
	$post_data['post_excerpt'] = $data['title'];
}
$post_data['tags'] = $data['tags'];
if (strlen($data['description'])) {
	$post_data['post_content'] = $data['description'];
}
if (! function_exists('get_currentuserinfo')) {
	function get_currentuserinfo ()
	{}
}
include_once (NGGALLERY_ABSPATH . 'admin/functions.php'); // admin functions
include_once (NGGALLERY_ABSPATH . 'admin/addgallery.php'); // nggallery_admin_add_gallery
//validate external image
if (! isAllowedUrl($data['img_url'])) {
	$json['errors'][] = "The file from {$data['img_url']} could not be uploaded.";
}
// WPMU action
if (nggAdmin::check_quota()) {
	$json['errors'][] = 'No space';
}
if (! (isset($json['errors']) && count($json['errors']))) {
	//get gallery with name snipi
	$gallery = wp_snipi_get_gallery(SNIPI_GALLERY_TITLE);
	if (! $gallery) {
		$json['errors'][] = 'Failure in database, no gallery with title' . SNIPI_GALLERY_TITLE;
		echo wp_snipi_json_encode($json);
		exit();
	}
	//set gallery id
	$galleryID = $gallery->gid;
	// Images must be an array
	$imageslist = array();
	// get the path to the gallery
	$gallerypath = $wpdb->get_var("SELECT path FROM $wpdb->nggallery WHERE gid = '$galleryID' ");
	if (! $gallerypath) {
		$json['errors'][] = 'Failure in database, no gallery path set !';
		echo wp_snipi_json_encode($json);
		exit();
	}
	// read list of images
	$dirlist = nggAdmin::scandir(WINABSPATH . $gallerypath);
	$filepart = pathinfo(strtolower($data['img_url']));
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
		$json['errors'][] = sprintf(__('Unable to write to directory %s. Is this directory writable by the server?', 'nggallery'), WINABSPATH . $gallerypath);
		echo wp_snipi_json_encode($json);
		exit();
	}
	//get file from snipi.com
	if (! LoadImageCURL($data['img_url'], $dest_file)) {
		$json['errors'][] = 'Error, the file could not be moved to : ' . $dest_file;
		echo wp_snipi_json_encode($json);
		exit();
	}
	//get metadata for uploaded file
	$img = getimagesize($dest_file);
	//verify that uploaded file has allowed mime type
	if (! ($img && preg_match('/^image\/(' . SNIPI_ALLOWED_IMAGE_EXT . ')$/', $img['mime']))) {
		unlink($dest_file);
		$json['errors'][] = 'Error, the file could not be moved to : ' . $dest_file;
		echo wp_snipi_json_encode($json);
		exit();
	}
	if (! nggAdmin::chmod($dest_file)) {
		$json['errors'][] = 'Error, the file permissions could not be set';
		echo wp_snipi_json_encode($json);
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
		//register NGG taxonomy before add tags
		$ngg->register_taxonomy();
		// update database
		$result = $wpdb->query("UPDATE $wpdb->nggpictures SET alttext = '{$post_data['post_title']}', description = '{$post_data['post_content']}' WHERE pid = {$image_ids[0]}");
		// add the tags
		wp_set_object_terms($image_ids[0], $post_data['tags'], 'ngg_tag');
		$json['success'] = true;
	}
}
echo wp_snipi_json_encode($json);
?>