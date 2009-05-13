<?php
/**
 * This file contains functions that show content for 'Overview' page
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.0.3
 * @since 1.0.0
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }


add_meta_box('dashboard_right_now', __('Status', 'snipi'), 'wp_snipi_overview_right_now', 'snipi_overview', 'left', 'core');
add_meta_box('dashboard_quick_press', __('Plugin mode', 'snipi'), 'wp_snipi_settings', 'snipi_overview', 'right', 'core');
add_meta_box('dashboard_primary', __('Latest News', 'snipi'), 'wp_snipi_overview_news', 'snipi_overview', 'left', 'core');

/**
 *
 */
function wp_snipi_overview()  {
?>
<div class="wrap ngg-wrap">
	<h2><?php _e('WP_SNIPI Plugin Overview', 'snipi') ?></h2>
	<div id="dashboard-widgets-wrap" class="ngg-overview">
	    <div id="dashboard-widgets" class="metabox-holder">
	    	<div id="side-info-column" class="inner-sidebar">
				<?php do_meta_boxes('snipi_overview', 'right', ''); ?>
			</div>
			<div id="post-body" class="has-sidebar">
				<div id="dashboard-widgets-main-content" class="has-sidebar-content">
				<?php do_meta_boxes('snipi_overview', 'left', ''); ?>
				</div>
			</div>
	    </div>
	</div>
</div>

<?php
}
/**
 * Show a summary of the used images
 *
 * @return void
 */
function wp_snipi_overview_right_now() {
		$api=wp_snipi_get_api();
		$url=wp_snipi_get_url();

    	if(isset($_POST['snipi_hidden'])&&$_POST['snipi_hidden'] == 'Y') {
    	    $un=(isset($_POST['snipi_user']))?trim(strip_tags($_POST['snipi_user'])):'';
    	    $pwd=(isset($_POST['snipi_password']))?trim(strip_tags($_POST['snipi_password'])):'';
    	    $errors=array();
    	    if (wp_snipi_update_user($un,$pwd,$api,$url)){
	            print_user_info();
    	    }
    	    else{
    	        print_user_login($errors,$un);
    	    }
	} else {
	if (wp_snipi_is_active($api)){
	        print_user_info();
		}else{
		    print_user_login($errors);
		}
	}
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
          <li><a class="rsswidget" title="" href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a>
		  <span class="rss-date"><?php echo date("F jS, Y", strtotime($item['pubdate'])); ?></span>
          <div class="rssSummary"><strong><?php echo human_time_diff(strtotime($item['pubdate'], time())); ?></strong> - <?php echo $item['description']; ?></div></li>
        <?php
        }
        echo "</ul>";
      }
      else
      {
          //@todo change domain for url
        ?>
        <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.', 'nggallery'), 'http://www.snipi.com/wordpress') ?></p>
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
    <div class="wrap">
    <h2>Activate plugin</h2>
    <p>
    After you have registered for Snipi and downloaded the Snipi Toolbar, enter your Snipi username and password here.  For more information on how to use the toolbar, read the <a href="admin.php?page=snipi-about">About section</a> of the Snipi for Wordpress plugin 
    </p>

    <?php
    if (count($errors)){
        echo "<ul>";
        foreach ($errors as $error) {
        	echo '<li>'.$error.'<li>';
        }
        echo "</ul>";
    }
    ?>
<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<p><?php _e("Snipi Username: " ); ?><input type="text" name="snipi_user" value="<?php echo $un; ?>" size="20"></p>
	<p><?php _e("Snipi Password: " ); ?><input type="password" name="snipi_password" value="" size="20"></p>
	<input type="hidden" name="snipi_hidden" value="Y">
	<p class="submit">
	<input type="submit" name="Submit" value="<?php _e('Update Options', 'snipi_trdom' ) ?>" />
	</p>
</form>
</div>
<?php
}

/**
 * Prints html code when user already activate plugin on Snipi.com
 *
 */
function print_user_info(){
    global $snipi;
    echo '<h2>Snipi plugin is active in '.(($snipi->options['mode']=='wp')?'WordPress Mode':'NextGen Mode').'.</h2>';
    echo "<p>You can either use the default WordPress Media Library (WordPress Mode), or for some extra power, download the NextGen Gallery plugin. As soon as its installed and activated, you can switch to NextGen Mode right here in the settings.</p>";
	echo "<p>Remember, you'll need the Snipi Toolbar to drag and drop images and have them automagically appear in WordPress.</p>";
	echo "<p>Enjoy.  And visit us at Snipi.com for all the other Snipi features.</p>";
}

/**
 * Print HTML code to switch mode in Snipi Plugin
 *
 */
function wp_snipi_settings(){
    global $snipi;

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
echo '<p>';
    switch($snipi->options['mode']){
        case 'ngg':
            echo 'Snipi for WordPress is currently in NextGen mode<br/>';
            echo ' Switch to <a href="'.str_replace('%7E','~',$_SERVER['PHP_SELF']).'?page=wp_snipi&mode=wp'.'">WordPress Mode</a>';
            break;
        case 'wp':

        default:
            echo 'Snipi for WordPress is currently in WordPress mode<br/>';
            if (class_exists('nggLoader')){
                echo ' Switch to <a href="'.str_replace('%7E','~',$_SERVER['PHP_SELF']).'?page=wp_snipi&mode=ngg'.'">NextGen Mode</a>';
            }
            break;
    }
    echo '</p>';
}
?>