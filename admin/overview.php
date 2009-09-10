<?php
/**
 * This file contains functions that show content for 'Overview' page
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 */
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die (_e('You are not allowed to call this page directly.', 'snipi'));
}
add_meta_box('dashboard_right_now', __('Status', 'snipi'), 'wp_snipi_overview_right_now', 'snipi_overview', 'left', 'core');
add_meta_box('dashboard_right_now', __('Test upload', 'snipi'), 'wp_snipi_overview_test_now', 'snipi_overview_test', 'left', 'core');
/**
 *
 */
function wp_snipi_overview ()
{
	?>
<div class="wrap">
	<div id="poststuff" class="metabox-holder">
		<h2><?php _e('Snipi for Wordpress Plugin Overview', 'snipi')?></h2>
		<div id="post-body">
			<div id="post-body-content">
				<?php do_meta_boxes('snipi_overview', 'left', '');?>
				<?php do_meta_boxes('snipi_overview_test', 'left', '');?>
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
	global $snipi;
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
/**
 * Prints form that sends test upload request or test results
 *
 */
function wp_snipi_overview_test_now ()
{
	$api = wp_snipi_get_api();
	$url = wp_snipi_get_url();
	if (isset($_POST['snipi_hidden']) && $_POST['snipi_hidden'] == 'T') {
		if (wp_snipi_test_upload($api, $url)) {
			print_wp_snipi_test_success();
		} else {
			print_wp_snipi_test_failed();
		}
	} else {
		print_wp_snipi_test_form();
	}
}
/**
 * Prints test upload successful result
 *
 */
function print_wp_snipi_test_success ()
{
	global $snipi;
	?>
<div class="wrap" style="padding: 10px">
	<h2><?php _e('Test successfully completed', 'snipi')?></h2>
	<p><?php _e('The sample image below should appear in your', 'snipi')?> <strong><?php
		echo (($snipi->options['mode'] == 'wp') ? _e('WordPress Media Library', 'snipi') : _e('NextGen Gallery', 'snipi'))?></strong>.
	</p>
	<img src="http://s3.amazonaws.com/snipi/images/a/160.png" alt="<?php _e('[SAMPLE IMAGE]', 'snipi')?>" /><br />
	<?php _e('[SAMPLE IMAGE]', 'snipi')?>
	<p><?php _e('If the sample image does not appear, please contact us at support@snipi.com.', 'snipi')?></p>
</div>
<?php
}
function print_wp_snipi_test_failed ()
{
	?>
<div class="wrap" style="padding: 10px">
	<h2><?php _e('Test wasn\'t successfully completed', 'snipi')?></h2>
	<p><?php _e('Please check the most common resons below:', 'snipi')?></p>
	<p><?php _e('Your site does not have public access.', 'snipi')?></p>
	<p><?php _e('In order to use file upload the chmod of wp-content folder must be set to 777. If you have not changed permissions in order to write in <tt>wp-content</tt> folder, <b>you will not be able to use Snipi for Wordpress Plugin</b>. If you do not know how to change this value, please read <a href="http://codex.wordpress.org/Changing_File_Permissions" title="Changing File Permissions">Changing File Permissions</a>.  Remember to reset the folder permissions after the <b>uploads</b> directory has been created inside wp-content.', 'snipi')?></p>
	<p><?php _e('If the sample image does not appear, please contact us at support@snipi.com.', 'snipi')?></p>
</div>
<?php
}

function print_wp_snipi_test_form ()
{
	?>
<div class="wrap" style="padding: 10px">
	<h2><?php _e('Test Snipi With Your WordPress Installation', 'snipi')?></h2>
	<p><?php _e('The below feature is provided to you as a way to test whether Snipi is working properly with your WordPress configuration.', 'snipi')?></p>
	<p><?php _e('Please click TEST below to send a sample image from the Snipi server to your WordPress Media Library.', 'snipi')?></p>
	<form name="wp_snipi_test_form" method="post" action="<?php	echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);?>">
		<input type="hidden" name="snipi_hidden" value="T">
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Test', 'snipi')?>" /></p>
	</form>
</div>
<?php
}
/**
 * Prints snipi login form
 *
 * @param array $errors
 * @param string $un Username for Snipi.com Account
 */
function print_user_login ($errors = array(), $un = '')
{
	?>
<div class="wrap" style="padding: 10px">
	<h2><?php _e('Activate plugin', 'snipi')?></h2>
	<p><?php _e('For more information on how to use the toolbar with Snipi for Wordpress Plugin, read the <a href="admin.php?page=snipi-about">About section</a> of the Snipi for Wordpress plugin', 'snipi')?></p>
	<p><?php _e('Please enter your <strong>Snipi username and password</strong>. If you do not yet have an account, <a href="http://www.snipi.com/registration" target="_blank">Sign Up here</a>.', 'snipi')?></p>
    <?php if (count($errors)) :?>
        <div id="login_error">
        <?php foreach ($errors as $error) {
			echo '<strong>'._e('ERROR', 'snipi').'</strong> : ' . $error . '<br/>';
		}?>
	    </div>
	<?php endif;?>
	<form name="wp_snipi_login_form" method="post"	action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);?>">
		<p><?php _e("Snipi Username", 'snipi');?>: <input type="text" name="snipi_user"	value="<?php echo $un;?>" size="20"></p>
		<p><?php _e("Snipi Password", 'snipi');?>: <input type="password" name="snipi_password" value="" size="20"></p>
		<input type="hidden" name="snipi_hidden" value="Y">
		<a href="http://www.snipi.com/login/reset"><?php _e('Lost your password?', 'snipi')?></a>
		<p class="submit">
			<input type="submit" name="Submit"	value="<?php _e('Sign in', 'snipi')?>" />
		</p>
	</form>
</div>
<?php
}
/**
 * Prints html code when user already activate plugin on Snipi.com
 *
 */
function print_user_info ()
{
	global $snipi, $snipi_username;
	if (isset($_GET['mode']) && strlen($_GET['mode'])) {
		//echo $snipi->options['mode'];
		switch ($_GET['mode']) {
			case 'ngg':
				if (class_exists('nggLoader')) {
					$snipi->options['mode'] = 'ngg';
					update_option('snipi_options', $snipi->options);
				} else {
					echo  _e('Please, Install NextGen', 'snipi');
				}
				break;
			case 'wp':
			default:
				$snipi->options['mode'] = 'wp';
				update_option('snipi_options', $snipi->options);
				break;
		}
	}
	echo '<div style="padding:10px">';
	echo '<h2>'.sprintf(__('Snipi plugin is active in <strong>%s Mode.</strong>','snipi'), ($snipi->options['mode'] == 'wp') ? 'WordPress' : 'NextGen');
	switch ($snipi->options['mode']) {
		case 'ngg':
			echo sprintf(__('Switch to <a href="%s?page=snipi-for-wordpress-media-library-and-nextgen-gallery&mode=wp">WordPress Mode</a>','snipi'),str_replace('%7E', '~', $_SERVER['PHP_SELF']));
			break;
		case 'wp':
		default:
			if (class_exists('nggLoader')) {
				echo sprintf(__('Switch to <a href="%s?page=snipi-for-wordpress-media-library-and-nextgen-gallery&mode=ngg">NextGen Mode</a>','snipi'),str_replace('%7E', '~', $_SERVER['PHP_SELF']));
			}
			break;
	}
	echo '</h2>';
	echo '<p style="float:left">';
	echo _e('Signed in as', 'snipi').' <strong>' . $snipi_username . '</strong>';
	echo '</p>';
	?>
	<form name="oscimp_form" method="post"	action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']);?>" style="float: left; padding: 7px;">
		<input type="hidden" name="snipi_hidden" value="R"/>
		<input class="button button-highlighted" type="submit" name="Submit" value="<?php _e('Sign out', 'snipi')?>" />
	</form>
<?php
	echo _e('<p style="clear:both">You can either use the default WordPress Media Library (WordPress Mode), or for some extra power, download the NextGen Gallery plugin. As soon as its installed and activated, you can switch to NextGen Mode right here in the settings.</p>
	<p>Remember, you\'ll need the Snipi Toolbar to drag and drop images and have them automagically appear in WordPress.</p>
	<p>Enjoy.  And visit us at Snipi.com for all the other Snipi features.</p>', 'snipi');
	echo '</div>';
}
?>