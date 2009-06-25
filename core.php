<?php
/**
 * This file contains core class and functions for WP Snipi plugin.
 *
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.1.1
 * @since 1.0.0
 */

/**
 * Class that handles core plugin functionality
 * This class based on nggLoader class by Alex Rabe http://alexrabe.boelinger.com/
 * @package wp_snipi
 */
class snipiLoader{

    var $version='1.0.0';
    var $minium_WP='2.7';
    var $minium_WPMU='2.7';
    var $updateURL='http://snipi.com/wordpress/version';
    var $options='';
    var $manage_page;

    /**
     * Class constructor
     *
     * @return snipiLoader
     */
    function snipiLoader(){

        // Load the language file
        $this->load_textdomain();

        // Load plugin options
        $this->load_options();

        // Stop the plugin if we missed the requirements
        if ((!$this->required_version())&&(!$this->check_memory_limit())) return;

        // Get some constants first
        $this->define_constant();
        $this->load_dependencies();

        // Init options & tables during activation & deregister init option
        register_activation_hook(dirname(__FILE__).'/wp_snipi.php',array(&$this,'activate'));
        register_deactivation_hook(dirname(__FILE__).'/wp_snipi.php',array(&$this,'deactivate'));

        // Register a uninstall hook to atumatic remove all tables & option
        if (function_exists('register_uninstall_hook')) register_uninstall_hook(dirname(__FILE__).'/wp_snipi.php',array('snipiLoader','uninstall'));

        // Start this plugin once all other plugins are fully loaded
        add_action('plugins_loaded',array(&$this,'start_plugin'));
    }

    /**
     * Start plugin
     *
     */
    function start_plugin(){

        // Content Filters
        add_filter('snipi_gallery_name','sanitize_title');

        // Load the admin panel or the frontend functions
        if (is_admin()){
            // Pass the init check or show a message
            if (get_option("snipi_init_check")!=false) add_action('admin_notices',create_function('','echo \'<div id="message" class="error"><p><strong>'.get_option("snipi_init_check").'</strong></p></div>\';'));
        }
    }

    /**
     * Verifies that plugin has all what it needs
     *
     * @return boolean
     */
    function required_version(){

        global $wp_version,$wpmu_version;

        // Check for WPMU installation
        if (!defined('IS_WPMU')) define('IS_WPMU',version_compare($wpmu_version,$this->minium_WPMU,'>='));

        // Check for WP version installation
        $wp_ok=version_compare($wp_version,$this->minium_WP,'>=');

        if (($wp_ok==FALSE) and (IS_WPMU!=TRUE)){
            add_action('admin_notices',create_function('','global $snipi; printf (\'<div id="message" class="error"><p><strong>\' . __(\'Sorry, NextGEN Gallery works only under WordPress %s or higher\', "snipi" ) . \'</strong></p></div>\', $snipi->minium_WP );'));
            return false;
        }
        if (!class_exists('nggLoader')&&$this->options['mode']=='ngg'){
            add_action('admin_notices',create_function('','printf (\'<div id="message" class="error"><p><strong>\' . __(\'Snipi plugin switched to WordPress mode. You need to install/activate NextGEN Gallery Plugin in order to use Snipi plugin in  NextGEN mode.\', "snipi" ) . \'</strong></p></div>\' );'));
            //switch to wordpress mode
            $this->options['mode']='wp';
            update_option('snipi_options',$this->options);
            return false;
        }
        return true;
    }

    /**
     * Checks memory limit
     *
     * @return boolean
     */
    function check_memory_limit(){

        $memory_limit=(int)substr(ini_get('memory_limit'),0,-1);
        //This works only with enough memory, 8MB is silly, wordpress requires already 7.9999
        if (($memory_limit!=0)&&($memory_limit<12)){
            add_action('admin_notices',create_function('','echo \'<div id="message" class="error"><p><strong>'.__('Sorry, Snipi works only with a Memory Limit of 16 MB higher',"snipi").'</strong></p></div>\';'));
            return false;
        }
        return true;
    }

    /**
     * Defines constants for plugin
     *
     */
    function define_constant(){

        define('SNIPI_ROOT',dirname(__FILE__));
        /*@todo set url here */
        define('SNIPI_AJAX_URL','http://www.snipi.com/wordpress/api/');
        define('SNIPI_ALLOWED_IMAGE_EXT','jpeg|jpg|gif|png');
        define('SNIPI_GALLERY_TITLE','Snipi');

        //TODO:SHOULD BE REMOVED LATER
        define('SNIPIVERSION',$this->version);
        define('SNIPIURL',$this->updateURL);

        // required for Windows & XAMPP
        if (!defined('WINABSPATH')) define('WINABSPATH',str_replace("\\","/",ABSPATH));

        // define URL
        define('SNIPIFOLDER',plugin_basename(dirname(__FILE__)));

        define('SNIPI_ABSPATH',str_replace("\\","/",WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__)).'/'));
        define('SNIPI_URLPATH',WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)).'/');

        // get value for safe mode
        if (!defined('WINABSPATH')) if ((gettype(ini_get('safe_mode'))=='string')){
            // if sever did in in a other way
            if (ini_get('safe_mode')=='off')
                define('SAFE_MODE',FALSE);
            else
                define('SAFE_MODE',ini_get('safe_mode'));
        }else
            define('SAFE_MODE',ini_get('safe_mode'));
    }

    /**
     * Loads dependencies for admin tool and for front end depend on which mode used
     */
    function load_dependencies(){
        global $wpdb;
        // Load backend libraries
        if (is_admin()){
            require_once (dirname(__FILE__).'/admin/admin.php');
            $this->snipiAdminPanel=new snipiAdminPanel();
            // Load frontend libraries
        }else{
            if (defined('SNIPI_API')){
                switch($this->options['mode']){
                    case 'ngg':
                        require_once (dirname (__FILE__) . '/lib/extention-ngg.php');
                        break;
                    case 'wp':
                        require_once (dirname(__FILE__).'/lib/extention-wp.php');
                        break;
                }
            }
        }
    }

    /**
     * Loads language file
     *
     */
    function load_textdomain(){
        load_plugin_textdomain('snipi',false,dirname(plugin_basename(__FILE__)).'/lang');
    }

    /*
     * Load plugin options to this object
     */
    function load_options(){
        $this->options=get_option('snipi_options');
    }

    /**
     * Handles activation
     */
    function activate(){
        include_once (dirname(__FILE__).'/admin/install.php');
        // check for tables
        snipi_install();
        // remove the update message
        delete_option('snipi_update_exists');
    }

     /**
     * Handles deactivation
     */
    function deactivate(){
        // remove & reset the init check option
        delete_option('snipi_init_check');
        delete_option('snipi_update_exists');
    }

    /**
     * Handles uninstall process
     */
    function uninstall(){
        include_once (dirname(__FILE__).'/admin/install.php');
        snipi_uninstall();
    }
}

/**
 * Username on Snipi.com
 */
$snipi_username='';

/**
 * Generate api for current user
 *
 * @return string Hash string
 */
function wp_snipi_get_api(){
    global $wpdb;
    $current_user=wp_get_current_user();
    $user_id=$current_user->ID;
    $user_pwd=$current_user->user_pass;
    $user_login=$current_user->user_login;
    $url=wp_snipi_get_url();

    $sql="SELECT MD5(CONCAT('$user_id','_','$user_login','_','$user_pwd','_".str_replace("'",'',AUTH_KEY).$url."')) as api";
    $result=$wpdb->get_results($sql);
    return $result[0]->api;
}

/**
 * Returns url for page that imports images from Snipi
 *
 * @return string
 */
function wp_snipi_get_url(){
    $url=get_option('siteurl').'/wp-content/plugins/snipi-for-wordpress-media-library-and-nextgen-gallery/wp_snipi.php';
    return $url;
}

/**
 * Sends request to Snipi.com to check that current user has active api
 *
 * @param string $api
 * @return boolean
 */
function wp_snipi_is_active($api){
    global $snipi_username;
    // create a new cURL resource
    $ch=curl_init();
    // set URL and other appropriate options
    $url=SNIPI_AJAX_URL.'?service=checkwpapi&api='.urlencode($api);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $buffer=curl_exec($ch);
    // close cURL resource, and free up system resources
    curl_close($ch);
    if (!empty($buffer)){
    	if (function_exists('json_decode'))
        	$obj=json_decode($buffer);
        else{
        	require_once (dirname(__FILE__).'/lib/json.php');
        	$json = new Services_JSON();
			$obj = $json->decode($buffer);
        }
        if ($obj->success=='true'&&$obj->wp_url==wp_snipi_get_url()){
            $snipi_username=$obj->username;
            return true;
        }
    }
    return false;
}

/**
 * Insert/update WP information for Snipi User
 *
 * @param string $un Snipi Username
 * @param string $pwd Snipi Password
 * @param string $api WP Snipi Plugin API
 * @param string $url WP url where to send image from snipi
 * @return boolean
 */
function wp_snipi_update_user($un,$pwd,$api,$url){
    global $snipi_username;
    // set URL and other appropriate options
    $servis_url=SNIPI_AJAX_URL.'?service=updatewpapi&username='.urlencode($un).'&password='.urlencode($pwd).'&url='.urlencode($url).'&api='.urlencode($api);
    // create a new cURL resource
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$servis_url);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $buffer=curl_exec($ch);
    // close cURL resource, and free up system resources
    curl_close($ch);
    if (!empty($buffer)){
        if (function_exists('json_decode'))
        	$obj=json_decode($buffer);
        else{
        	require_once (dirname(__FILE__).'/lib/json.php');
        	$json = new Services_JSON();
			$obj = $json->decode($buffer);
        }
        if ($obj->success=='true'){
            $snipi_username=$obj->username;
            return true;
        }
    }
    return false;
}

/**
 * Sends request to Snipi.com in order to execute test upload
 * @param $api
 * @param $url
 * @return boolean
 */
function wp_snipi_test_upload($api,$url){
    // set URL and other appropriate options
    $servis_url=SNIPI_AJAX_URL.'?service=testwpapi&url='.urlencode($url).'&api='.urlencode($api);
    // create a new cURL resource
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$servis_url);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $buffer=curl_exec($ch);
    // close cURL resource, and free up system resources
    curl_close($ch);
    if (!empty($buffer)){
        if (function_exists('json_decode'))
        	$obj=json_decode($buffer);
        else{
        	require_once (dirname(__FILE__).'/lib/json.php');
        	$json = new Services_JSON();
			$obj = $json->decode($buffer);
        }
        if ($obj->success=='true'){
            return true;
        }
    }
    return false;
}

/**
 * Remove any reference to this plugin activation on Snipi.com
 *
 * @param string $api
 * @param string $url
 * @return unknown
 */
function wp_snipi_remove_user($api,$url){
    // set URL and other appropriate options
    $servis_url=SNIPI_AJAX_URL.'?service=removeapi&url='.urlencode($url).'&api='.urlencode($api);
    // create a new cURL resource
    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$servis_url);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $buffer=curl_exec($ch);
    // close cURL resource, and free up system resources
    curl_close($ch);
    if (!empty($buffer)){
        if (function_exists('json_decode'))
        	$obj=json_decode($buffer);
        else{
        	require_once (dirname(__FILE__).'/lib/json.php');
        	$json = new Services_JSON();
			$obj = $json->decode($buffer);
        }
        if ($obj->success=='true'){
            return true;
        }
    }
    return false;
}

/**
 * Get information about user who has particular api
 * @param string $api
 * @return array
 */
function wp_snipi_get_user($api){
    global $wpdb;
    $url=wp_snipi_get_url();
    $sql="SELECT * FROM $wpdb->users WHERE MD5(CONCAT(ID,'_',user_login,'_',user_pass,'_".str_replace("'",'',AUTH_KEY).$url."'))='$api' LIMIT 1";
    $result=$wpdb->get_results($sql);
    return (is_array($result)&&count($result))?$result[0]:array();
}

/**
 * Return gallery with specified title. If gallery with specified title does not exist creates it
 *
 * @param string $title Gallery title
 * @param integer $user_ID User id. The owner of new gallery will be user with this id
 * @return object
 */
function wp_snipi_get_gallery($title,$user_ID){
    global $ngg;
    $gallery=nggdb::find_gallery($title);
    if ($gallery===false){
        //create new gallery with name Snipi
        $defaultpath=$ngg->options['gallerypath'];
        //eliminate any html output from nextgen
        ob_start();
        nggAdmin::create_gallery($title,$defaultpath);
        ob_end_clean();
        $gallery=nggdb::find_gallery($title);
    }
    return (is_object($gallery))?$gallery:null;
}

/**
 * Grab file form remote server
 *
 * @since 1.1.1
 * @param string $res remote $url
 * @param string $des local path
 * @return boolean
 */
function LoadImageCURL ($res, $des)
{
    $ch = curl_init($res);
    $fp = fopen($des, "wb");
    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return file_exists($des);
}

/**
 * check if image url belongs to Snipi
 * @since 1.1.1
 * @param string $img_url
 * @return boolean
 */
function isAllowedUrl($img_url){
    return preg_match("/^http:\/\/s3\.amazonaws\.com\/snipi\/images\/a\/[\d]+\.(".SNIPI_ALLOWED_IMAGE_EXT.")$/",$img_url);
}
?>