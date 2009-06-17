=== Snipi for Wordpress ===
Contributors: Snipi, Denis Uraganov
Tags: photo, image, picture, pics, images, post, media library, wordpress, gallery, photos, drag and drop, upload, send, nextgen, facebook, twitter, youtube, vimeo, firefox, add on, iphone, plugin
Donate link:
Requires at least: 2.7
Tested up to: 2.8
Stable tag: 1.1.4

Drag and drop images into the Snipi Toolbar and they automatically appear in your WordPress Media Library or NextGen Gallery.

== Description ==

Using the Snipi Toolbar, you can drag and drop images from anywhere on the Internet and have them appear automatically in your WordPress Media Library or NextGen Gallery.

You can get the Snipi Toolbar at www.snipi.com.

The Snipi Toolbar is a Firefox 3 (or higher) add-on that allows you to literally drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web.  The current version of the Snipi WordPress Plugin gives you the option of having an entire list of images or a single image at a time sent to WordPress' Media Library or NextGen Gallery.  Within seconds after dragging an image into the Snipi Toolbar, the image will automatically appear in WordPress, ready for you to use in your posts.

The Snipi WordPress Plugin has two modes, WordPress mode or NGG mode.  If you are an NextGen Gallery user, you can choose NGG mode to have images sent to NextGen Gallery rather than the WordPress Media Libary.

The Snipi Toolbar also allows you to drag and drop products, photos and YouTube and Vimeo videos from anywhere on the web. These items are always available with you in the Snipi Toolbar, are saved on Snipi.com, can be sent to Facebook or WordPress, can be sent to Twitter (Tweeted) on the fly, and are available on your iPhone using the Snipi app.

Important Links:

* <a href="http://www.snipiblog.com/tutorial-videos/" title="Tutorial videos">Snipi Overview Videos</a>
* <a href="http://www.youtube.com/watch?v=73iNmJ1k-2c" title="Wordpress Support Forum">Snipi For WordPress Video</a>
* <a href="http://wordpress.org/tags/snipi-for-wordpress-media-library-and-nextgen-gallery" title="Wordpress Support Forum">Support Forum</a>

RELEASE INFORMATION
---------------
Snipi for Wordpress 1.1.4.
Released on June 17, 2009.

CHANGELOG: 1.1.4
----------------
* Resolved issue with Overview page


FEATURES:

* Drag and drop images from anywhere on the Internet into the Snipi Toolbar and have them automatically sent to WordPress.
* WordPress Media Library Mode or NextGen Gallery Mode.
* Use the Snipi Toolbar for capturing products, photos and videos
* Send photos and videos to Facebook, Wordpress or Tweet them, right from the Toolbar.
* Use the Snipi iPhone app to view all of your products, photos and videos - on the go.

== Installation ==

Steps:

1. Download the plug-in.
1. Upload the files to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to "Overview", under Snipi and sign in using your Snipi username and password. If you do not yet have a Snipi account or have not yet downloaded the Snipi Toolbar for Firefox 3 (or higher), go to www.snipi.com, register, and install the Toolbar for Firefox.
1. Set the Snipi WordPress plugin to either WordPress or NGG mode (depending on whether you use the WordPress Media Library or NextGen Gallery).
1. Open up your Snipi Toolbar in Firefox by clicking the Snipi logo in the lower right hand corner of your Firefox window. Sign in.
1. Create a new list in the Snipi Toolbar, select "Send these photos to Wordpress".  If you have not yet signed into Snipi in Wordpress (step 3 above) then you will not be able to check this box.
1. Drag and image from anywhere on the Internet into the toolbar.  Click save in the Toolbar.
1. Voila!  In about 60 seconds, that image will appear in either the WordPress Media Library or the NextGen Gallery (depending on which mode you choose/prefer).

== Features Configuration ==
Modify configuration variables in  /wp_snipi/core.php

Make sure that the following constants set properly:

`SNIPI_AJAX_URL` - Url that checks snipi user profile (For example, http://www.snipi.com/wordpress/api/)
`SNIPI_ALLOWED_IMAGE_EXT` - Allowed Image extentions. Use pipe  as separator (For example, jpeg|jpg|gif|png);
`SNIPI_GALLERY_TITLE` - Title of the gallery where plugin put images from Snipi (for NextGen Gallery mode only)

== Frequently Asked Questions ==

= I have some issue with Snipi for WordPress Plugin. What should I do? =

If you are having any problems with the Snipi for WordPress plugin, please do not hesitate to email our support team.

Our team works hard to test the Snipi for WordPress plugin with different configurations. However, it's impossible to test our plugin with every possible configuration.

Therefore, we appreciate the effort you put forth to share any problem that you have with Snipi for Wordpress Plugin. Please send the description of your problem to support@snipi.com Also, please include the following information that will help us to solve your issue:  WordPress version, Snipi for WordPress version, and , if applicable, the NextGen Gallery version you are using.

== Screenshots ==

<a href="http://www.youtube.com/watch?v=73iNmJ1k-2c" title="Wordpress Support Forum">Snipi For WordPress Video</a>

== ChangeLog ==

Release: 1.1.4:
* Resolved issue with Overview page

Release: 1.1.3:
* Compatibility 2.8 

Release: 1.1.2:
* Fixed minor bugs

Release: 1.1.1:
* Resolved issue with PHP Allow URL fopen
* Improved security


Release: 1.1.0:
* Support PHP 4
* Improved activation/deactivation processes on Snipi.com
* Cosmetic changes for user interface
