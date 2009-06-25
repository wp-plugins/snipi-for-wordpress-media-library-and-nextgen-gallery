<?php
/**
 * This file contains functions that show content for 'Overview' page
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.1.5
 * @since 1.0.0
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


add_meta_box('dashboard_right_now', __('Status', 'snipi'), 'wp_snipi_overview_right_now', 'snipi_overview', 'left', 'core');
add_meta_box('dashboard_right_now', __('Test upload', 'snipi'), 'wp_snipi_overview_test_now', 'snipi_overview_test', 'left', 'core');
//add_meta_box('dashboard_quick_press', __('Plugin mode', 'snipi'), 'wp_snipi_settings', 'snipi_overview', 'right', 'core');
//add_meta_box('dashboard_primary', __('Latest News', 'snipi'), 'wp_snipi_overview_news', 'snipi_overview', 'left', 'core');

/**
 *
 */
function wp_snipi_overview()  {
?>
<div class="wrap">
<div id="poststuff" class="metabox-holder">
<h2><?php _e('Snipi for Wordpress Plugin Overview', 'snipi') ?></h2>
<div id="post-body">
<div id="post-body-content">
<?php do_meta_boxes('snipi_overview', 'left', ''); ?>
<?php do_meta_boxes('snipi_overview_test', 'left', ''); ?>
</div>
</div>
</div>
</div>

<?php
}
/**
 * Show a snipi login form and switch mode links
 *
 * @return void
 */
function wp_snipi_overview_right_now ()
{
    $api = wp_snipi_get_api();
    $url = wp_snipi_get_url();
    $errors = array();
    if (isset($_POST['snipi_hidden']) && $_POST['snipi_hidden'] == 'Y') {
        $un = (isset($_POST['snipi_user'])) ? trim(strip_tags($_POST['snipi_user'])) : '';
        $pwd = (isset($_POST['snipi_password'])) ? trim(strip_tags($_POST['snipi_password'])) : '';
        if (wp_snipi_update_user($un, $pwd, $api, $url)) {
            print_user_info();
        } else {
            print_user_login(array('Incorrect username or password'), $un);
        }
    } elseif (isset($_POST['snipi_hidden']) && $_POST['snipi_hidden'] == 'R') {
        if (wp_snipi_remove_user($api, $url)) {
            print_user_login(array(), '');
        }
    } else {
        if (wp_snipi_is_active($api)) {
            print_user_info();
        } else {
            print_user_login($errors);
        }
    }
}


function  wp_snipi_overview_test_now(){
 $api = wp_snipi_get_api();
    $url = wp_snipi_get_url();
    $errors = array();
    if (isset($_POST['snipi_hidden']) && $_POST['snipi_hidden'] == 'T') {
    if (wp_snipi_test_upload($api,$url)) {
            print_wp_snipi_test_success();
        } else {
            print_wp_snipi_test_failed();
        }
    } else {
    	print_wp_snipi_test_form();
    }
}

function print_wp_snipi_test_success(){
	global $snipi
	?>
			<div class="wrap" style="padding: 10px">
<h2>Test completed </h2>

<p>
The sample image below should appear in your <strong><?php echo (($snipi->options['mode']=='wp')?'WordPress Media Library':'NextGen Gallery')?></strong>.
</p>
<img src="http://s3.amazonaws.com/snipi/images/a/160.png" alt="sample image"/><br/>
[SAMPLE IMAGE]
<p>
If the sample image does not appear, please contact us at support@snipi.com.
</p>
</div>

<?php 
}

function print_wp_snipi_test_failed(){
	?>
	<div class="wrap" style="padding: 10px">
<h2>Test completed </h2>

<p>
The sample image below should appear in your <strong><?php echo (($snipi->options['mode']=='wp')?'WordPress Media Library':'NextGen Gallery')?></strong>.
</p>
<img src="http://s3.amazonaws.com/snipi/images/a/160.png" alt="sample image"/><br/>
[SAMPLE IMAGE]
<p>
If the sample image does not appear, please contact us at support@snipi.com.
</p>
</div>
<?php 
};
            
function print_wp_snipi_test_form(){
	?>
	<div class="wrap" style="padding: 10px">
<h2>Test Snipi With Your WordPress Installation</h2>
<p>The below feature is provided to you as a way to test whether Snipi is
working properly with your WordPress configuration.</p>

<p>Please click TEST below to send a sample image from the Snipi server
to your WordPress Media Library.</p>
    
<form name="wp_snipi_test_form" method="post"
	action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="snipi_hidden" value="T"> 
<p class="submit"><input type="submit" name="Submit"
	value="<?php _e('Test', 'snipi' ) ?>" /></p>
</form>
</div>
<?php 
}


function print_wp_snipi_test_result(){
	
}

/**
 * Show the latest news
 *
 * @return void
 */
function wp_snipi_overview_news(){
	// get feed_messages
	require_once(ABSPATH . WPINC . '/rss.php');
?>
<div class="rss-widget">
    <?php
      $rss = @fetch_rss('http://www.snipi.com/wordpress/rss/');

      if ( isset($rss->items) && 0 != count($rss->items) )
      {
        $rss->items = array_slice($rss->items, 0, 3);
        echo "<ul>";
		foreach ($rss->items as $item)
        {
        ?>
          <li><a class="rsswidget" title=""
	href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a>
<span class="rss-date"><?php echo date("F jS, Y", strtotime($item['pubdate'])); ?></span>
<div class="rssSummary"><strong><?php echo human_time_diff(strtotime($item['pubdate'], time())); ?></strong> - <?php echo $item['description']; ?></div>
</li>
        <?php
        }
        echo "</ul>";
      }
      else
      {
          //@todo change domain for url
        ?>
        <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.', 'nggallery'), 'http://snipi.com/tour/view/social/') ?></p>
        <?php
      }
    ?>
</div>
<?php
}

/**
 * Show snipi user information
 *
 */
function wp_snipi_overview_user(){
   		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$user_pwd= $current_user->user_pass;
		$user_login= $current_user->user_login;
		$api=wp_snipi_get_api();
		echo 'Password hash: '.$user_pwd.'<br>';
		echo '<hr/>';
		echo 'User login: '.$user_login.'<br>';
		echo '<hr/>';
		echo 'Url: '.wp_snipi_get_url().'<br>';
		echo '<hr/>';
		echo 'Api: '.$api.'<br>';
		echo '<hr/>';
}

/**
 * Prints snipi login form
 *
 * @param array $errors
 * @param string $un Username for Snipi.com Account
 */
function print_user_login($errors=array(),$un=''){
    ?>
<div class="wrap" style="padding: 10px">
<h2>Activate plugin</h2>
<p>For more information on how to use the toolbar with Snipi for
Wordpress Plugin, read the <a href="admin.php?page=snipi-about">About
section</a> of the Snipi for Wordpress plugin</p>

<p>Please enter your <strong>Snipi username and password</strong>. If you do not yet have
an account, <a href="http://www.snipi.com/registration" target="_blank">Sign
Up here</a>.</p>
    <?php
    if (count($errors)):?>
        <div id="login_error">
        <?php   foreach ($errors as $error) {
        echo '<strong>ERROR</strong> : '.$error.'<br/>';
        ?>
		</div>
    <?php }?>
    <?php endif;?>
    
<form name="wp_snipi_login_form" method="post"
	action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<p><?php _e("Snipi Username: " ); ?><input type="text" name="snipi_user"
	value="<?php echo $un; ?>" size="20"></p>
<p><?php _e("Snipi Password: " ); ?><input type="password"
	name="snipi_password" value="" size="20"></p>
<input type="hidden" name="snipi_hidden" value="Y"> <a
	href="http://www.snipi.com/login/reset">Lost your password?</a>
<p class="submit"><input type="submit" name="Submit"
	value="<?php _e('Sign in', 'snipi_trdom' ) ?>" /></p>
</form>
</div>
<?php
}

/**
 * Prints html code when user already activate plugin on Snipi.com
 *
 */
function print_user_info(){
    global $snipi,$snipi_username;
  if (isset($_GET['mode'])&&strlen($_GET['mode'])){
        //echo $snipi->options['mode'];
        switch($_GET['mode']){
            case 'ngg':
                if (class_exists('nggLoader')){
                    $snipi->options['mode']='ngg';
                    update_option('snipi_options',$snipi->options);
                }else{
                    echo 'install NextGen';
                }
                break;
            case 'wp':
            default:
                $snipi->options['mode']='wp';
                update_option('snipi_options',$snipi->options);
                break;
        }
    }
    echo '<div style="padding:10px">';
    echo '<h2>Snipi plugin is active in <strong>'.(($snipi->options['mode']=='wp')?'WordPress Mode':'NextGen Mode').'</strong>. ';
    switch($snipi->options['mode']){
        case 'ngg':
            echo ' Switch to <a href="'.str_replace('%7E','~',$_SERVER['PHP_SELF']).'?page=snipi-for-wordpress-media-library-and-nextgen-gallery&mode=wp'.'">WordPress Mode</a>';
            break;
        case 'wp':
        default:
            if (class_exists('nggLoader')){
                echo ' Switch to <a href="'.str_replace('%7E','~',$_SERVER['PHP_SELF']).'?page=snipi-for-wordpress-media-library-and-nextgen-gallery&mode=ngg'.'">NextGen Mode</a>';
            }
            break;
    }
   echo '</h2>';
    echo '<p style="float:left">Signed in as <strong>'.$snipi_username.'</strong>.</p>';
?>
<form name="oscimp_form" method="post"
	action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>"
	style="float: left; padding: 7px;"><input type="hidden"
	name="snipi_hidden" value="R"> <input class="button button-highlighted"
	type="submit" name="Submit"
	value="<?php _e('Sign out', 'snipi_trdom' ) ?>" /></form>
<?php
    echo "<p style=\"clear:both\">You can either use the default WordPress Media Library (WordPress Mode), or for some extra power, download the NextGen Gallery plugin. As soon as its installed and activated, you can switch to NextGen Mode right here in the settings.</p>";
	echo "<p>Remember, you'll need the Snipi Toolbar to drag and drop images and have them automagically appear in WordPress.</p>";
	echo "<p>Enjoy.  And visit us at Snipi.com for all the other Snipi features.</p>";
	echo '</div>';
}
?>