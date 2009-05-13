<?php
/**
 * This file contains functions used to show "About" page
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.0.3
 * @since 1.0.0
 */
if (preg_match ( '#' . basename ( __FILE__ ) . '#', $_SERVER ['PHP_SELF'] )) {
	die ( 'You are not allowed to call this page directly.' );
}

/**
 * Prints HTML code for "About" page
 *
 */
function wp_snipi_about() {
	echo "<h2>About</h2>";
	echo "<p>Using the Snipi Toolbar, you can drag and drop images from anywhere on the Internet and have them appear automatically in your WordPress Media Library or NextGen Gallery.</p>";
	echo "<p>You can get the Snipi Toolbar at www.snipi.com.</p>";
	echo "<p>The Snipi Toolbar is a Firefox 3 (or higher) add-on that allows you to literally drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web.  The current version of the Snipi WordPress Plugin gives you the option of having an entire list of images or a single image at a time sent to WordPress' Media Library or NextGen Gallery.  Within seconds after dragging an image into the Snipi Toolbar, the image will automatically appear in WordPress, ready for you to use in your posts.</p>";
	echo "<p>The Snipi WordPress Plugin has two modes, WordPress mode or NGG mode.  If you are an NextGen Gallery user, you can choose NGG mode to have images sent to NextGen Gallery rather than the WordPress Media Libary.</p>";
	echo "<p>The Snipi Toolbar also allows you to drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web. These items are always available with you in the Snipi Toolbar, are saved on Snipi.com, can be sent to Facebook or WordPress, can be sent to Twitter (Tweeted) on the fly, and are available on your iPhone using the Snipi app.</p>";
	echo "<p>Watch the tutorial videos at http://www.snipiblog.com/tutorial-videos/</p>";
	echo "<p>Features:</p>";
	echo "<ul><li>Drag and drop images from anywhere on the Internet into the Snipi Toolbar and have them automatically sent to WordPress.<li>";
	echo "<li>WordPress Media Library Mode or NextGen Gallery Mode.<li>";
	echo "<li>Use the Snipi Toolbar for capturing products, photos and videos.<li>";
	echo "<li>send photos and videos to Facebook, Wordpress or Tweet them, right from the Toolbar.<li>";
	echo "<li>Use the Snipi iPhone app to view all of your products, photos and videos - on the go.<li></ul>";
	
	echo "<h2>Installation</h2>";
	echo "<p>1. Download the plug-in.</p>";
	echo "<p>2. Upload the files to wp-content/plugins/</p>";
	echo "<p>3. Go to “Overview”, under Snipi and sign in using your Snipi username and password. If you do not yet have a Snipi account or have not yet downloaded the Snipi Toolbar for Firefox 3 (or higher), go to www.snipi.com, register, and install the Toolbar for Firefox.</p>";
	echo "<p>4.  Set the Snipi WordPress plugin to either WordPress or NGG mode (depending on whether you use the WordPress Media Library or NextGen Gallery).</p>";
	echo "<p>5.  Open up your Snipi Toolbar in Firefox by clicking the Snipi logo in the lower right hand corner of your Firefox window. Sign in. </p>";
	echo "<p>6. Create a new list in the Snipi Toolbar, select \"Send these photos to Wordpress\".  If you have not yet signed into Snipi in Wordpress (step 3 above) then you will not be able to check this box.</p>";
	echo "<p>7. Drag and image from anywhere on the Internet into the toolbar.  Click save in the Toolbar.</p>";
	echo "<p>8. Voila!  In about 60 seconds, that image will appear in either the WordPress Media Library or the NextGen Gallery (depending on which mode you choose/prefer).</p>";
}
?>