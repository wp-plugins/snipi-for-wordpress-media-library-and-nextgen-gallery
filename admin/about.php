<?php
/**
 * This file contains functions used to show "About" page
 * @package wp_snipi
 * @author Denis Uraganov <snipi@uraganov.net>
 * @version 1.2.0
 * @since 1.0.0
 */
if (preg_match ( '#' . basename ( __FILE__ ) . '#', $_SERVER ['PHP_SELF'] )) {
	die (_e('You are not allowed to call this page directly.', 'snipi'));
}

/**
 * Prints HTML code for "About" page
 *
 */
function wp_snipi_about() {
?>
<div class="wrap">
	<div id="poststuff" class="metabox-holder has-right-sidebar">
		<div id="post-body">
			<div id="post-body-content">
				<div class="wrap" style="padding: 10px">
					<h2><?php _e('About', 'snipi') ?></h2>
					<?php _e('
					<p>Using the Snipi Toolbar, you can drag and drop images from anywhere on the Internet and have them appear automatically in your WordPress Media Library or NextGen Gallery.</p><p>You can get the Snipi Toolbar at www.snipi.com.</p><p>The Snipi Toolbar is a Firefox 3 (or higher) add-on that allows you to literally drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web.  The current version of the Snipi WordPress Plugin gives you the option of having an entire list of images or a single image at a time sent to WordPress\' Media Library or NextGen Gallery.  Within seconds after dragging an image into the Snipi Toolbar, the image will automatically appear in WordPress, ready for you to use in your posts.</p>
					<p>The Snipi WordPress Plugin has two modes, WordPress mode or NGG mode.  If you are an NextGen Gallery user, you can choose NGG mode to have images sent to NextGen Gallery rather than the WordPress Media Libary.</p>
					<p>The Snipi Toolbar also allows you to drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web. These items are always available with you in the Snipi Toolbar, are saved on Snipi.com, can be sent to Facebook or WordPress, can be sent to Twitter (Tweeted) on the fly, and are available on your iPhone using the Snipi app.</p>
					<p>Watch the tutorial videos at http://www.snipiblog.com/tutorial-videos/</p>
					', 'snipi') ?>
					<?php _e('
					<p>Features:</p>
					<ul>
						<li>Drag and drop images from anywhere on the Internet into the Snipi Toolbar and have them automatically sent to WordPress.</li>
						<li>WordPress Media Library Mode or NextGen Gallery Mode.</li>
						<li>Use the Snipi Toolbar for capturing products, photos and videos.</li>
						<li>send photos and videos to Facebook, Wordpress or Tweet them, right from the Toolbar.</li>
						<li>Use the Snipi iPhone app to view all of your products, photos and videos - on the go.</li>
					</ul>
					', 'snipi') ?>
				</div>
				<div class="wrap" style="padding: 10px">
					<h2><?php _e('Installation', 'snipi') ?></h2>
					<?php _e('
					<p>1. Download the plug-in.</p>
					<p>2. Upload the files to wp-content/plugins/</p>
					<p>3. Go to &quot;Overview&quot;, under Snipi and sign in using your Snipi username and password. If you do not yet have a Snipi account or have not yet downloaded the Snipi Toolbar for Firefox 3 (or higher), go to www.snipi.com, register, and install the Toolbar for Firefox.</p>
					<p>4. Set the Snipi WordPress plugin to either WordPress or NGG mode (depending on whether you use the WordPress Media Library or NextGen Gallery).</p>
					<p>5. Open up your Snipi Toolbar in Firefox by clicking the Snipi logo in the lower right hand corner of your Firefox window. Sign in. </p>
					<p>6. Create a new list in the Snipi Toolbar, select \"Send these photos to Wordpress\".  If you have not yet signed into Snipi in Wordpress (step 3 above) then you will not be able to check this box.</p>
					<p>7. Drag and image from anywhere on the Internet into the toolbar.  Click save in the Toolbar.</p>
					<p>8. Voila!  In about 60 seconds, that image will appear in either the WordPress Media Library or the NextGen Gallery (depending on which mode you choose/prefer).</p>
					', 'snipi') ?>
				</div>
				<div class="wrap" style="padding: 10px">
				    <h2><?php _e('Frequently Asked Questions', 'snipi') ?></h2>
				    <?php _e('
				    <p>==I have some issue with Snipi for WordPress Plugin. What should I do?==</p>
				    <p>Our team works hard to test our plugin with different configurations.</p>
				    <p>However it is physically impossible to test our plugin with all possible configurations.</p>
				    <p>We will appreciate your effort to share with us any problem that you have with Snipi for Wordpress Plugin.</p>
				    <p>Please send description of your problem to support@snipi.com</p>
				    <p>Also include the following information that will help us to solve your issue:</p>
				    <ul>
				    <li>WordPress version</li>
				    <li>Snipi for WordPress version</li>
				    <li>NextGen Gallery version (if you use one)</li>
				    </ul>
				    ', 'snipi') ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
}
?>