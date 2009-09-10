<?php
/**
 * This file contains functions and includes files required for url image upload using WordPress Gallery
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 */
global $wpdb, $user_ID, $img, $snipi;
$wp_snipi_options = get_option('snipi_options');
$json = array('success' => false);
$data_keys = array('api' , 'img_url' , 'title' , 'description');
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
	$json['errors'][] = 'Error, API is not valid: ' . $data['api'];
}
if (strlen($data['title'])) {
	$post_data['post_title'] = $data['title'];
	$post_data['post_excerpt'] = $data['title'];
}
if (strlen($data['description'])) {
	$post_data['post_content'] = $data['description'];
}
if (! (isset($json['errors']) && count($json['errors']))) {
	$id = wp_snipi_media_handle_upload($data['img_url'], 0, $post_data);
	if (is_wp_error($id)) {
		foreach ($id->errors['upload_error'] as $error) {
			$json['errors'][] = $error;
		}
	} else {
		$json['success'] = true;
	}
}
echo wp_snipi_json_encode($json);
/**
 * Function handles image upload from url. Verifies and Uploads image file from url, creates thumbnails and post
 *
 * @param string $media_url
 * @param integer $post_id
 * @param array $post_data
 * @return integer Post id
 */
function wp_snipi_media_handle_upload ($media_url, $post_id, $post_data = array())
{
	global $wpdb, $img;
	$overrides = array('test_form' => false);
	$time = current_time('mysql');
	$file = wp_snipi_handle_upload($media_url, $overrides, $time);
	if (isset($file['error']))
		return new WP_Error('upload_error', $file['error']);
	$url = $file['url'];
	$type = $img['mime'];
	$file = $file['file'];
	$title = preg_replace('/\.[^.]+$/', '', basename($file));
	$content = '';
	require_once (ABSPATH . 'wp-admin/includes/image.php');
	// use image exif/iptc data for title and caption defaults if possible
	if ($image_meta = wp_read_image_metadata($file)) {
		if (trim($image_meta['title']))
			$title = $image_meta['title'];
		if (trim($image_meta['caption']))
			$content = $image_meta['caption'];
	}
	// Construct the attachment array
	$attachment = array_merge(array('post_mime_type' => $type , 'guid' => $url , 'post_parent' => $post_id , 'post_title' => $title , 'post_content' => $content), $post_data);
	// Save the data
	$id = wp_insert_attachment($attachment, $file, $post_id);
	if (! is_wp_error($id)) {
		wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $file));
	}
	return $id;
}
/**
 * @since unknown
 *
 * @param string $img_url Image url
 * @param array $overrides Optional. An associative array of names=>values to override default variables with extract( $overrides, EXTR_OVERWRITE ).
 * @return array On success, returns an associative array of file attributes. On failure, returns $overrides['upload_error_handler'](&$file, $message ) or array( 'error'=>$message ).
 */
function wp_snipi_handle_upload (&$img_url, $overrides = false, $time = null)
{
	global $img;
	// The default error handler.
	if (! function_exists('wp_handle_upload_error')) {
		function wp_handle_upload_error (&$img_url, $message)
		{
			return array('error' => $message);
		}
	}
	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';
	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;
	// Install user overrides. Did we mention that this voids your warranty?
	if (is_array($overrides))
		extract($overrides, EXTR_OVERWRITE);
		// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if (! (($uploads = wp_upload_dir($time)) && false === $uploads['error']))
		return $upload_error_handler($img_url, $uploads['error']);
		//allow upload files only from snipi.com and only files with specified extentions
	if (! isAllowedUrl($img_url)) {
		return $upload_error_handler($img_url, sprintf('The file from %s could not be uploaded. _1', $img_url));
	}
	$filepart = pathinfo(strtolower($img_url));
	$filepart['filename'] = substr($filepart["basename"], 0, strlen($filepart["basename"]) - (strlen($filepart["extension"]) + 1));
	$filename = sanitize_title($filepart['filename']) . '.' . $filepart['extension'];
	$filename = wp_unique_filename($uploads['path'], $filename, $unique_filename_callback);
	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	//get file from snipi.com
	if (! LoadImageCURL($img_url, $new_file)) {
		return $upload_error_handler($img_url, sprintf('The file from %s could not be uploaded. _2.', $img_url));
	}
	//get metadata for uploaded file
	$img = @getimagesize($new_file);
	//verify that uploaded file has allowed mime type
	if (! ($img && preg_match('/^image\/(' . SNIPI_ALLOWED_IMAGE_EXT . ')$/', $img['mime']))) {
		unlink($new_file);
		unset($img);
		return $upload_error_handler($img_url, sprintf('The file from %s could not be uploaded. _3', $img_url));
	}
	// Set correct file permissions
	$stat = stat(dirname($new_file));
	$perms = $stat['mode'] & 0000666;
	@chmod($new_file, $perms);
	// Compute the URL
	$url = $uploads['url'] . "/$filename";
	$return = apply_filters('wp_handle_upload', array('file' => $new_file , 'url' => $url));
	return $return;
}
?>