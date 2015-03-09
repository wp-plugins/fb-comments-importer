=== FB Comments Importer ===
Contributors: ivan.m89
Donate link: 
Tags: fb comments, facebook, comments, fb comments import, facebook comments, facebook comments import, discussion
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 1.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import Facebook comments into Wordpress.

== Description ==

FB Comments Importer is a simple plugin that lets you import comments from your Facebook page directly into a Wordpress comments system.
It gives your site an SEO boost and it makes it look even more alive.

We also have a **PRO** version with additional features:

* Import comments from regular posts, images or statuses
* Import from pages, groups or profiles (import from profiles are limited for now because of changes in fb API)
* Manually connect post with comments
* Import from older posts
* Automate the import process (with WP cron)
* Turn the cron on/off or restart it
* Faster and more efficient support

For any issues with the plugin, please open a support ticket and we will fix it ASAP!

== Installation ==

1. Unzip `fb-comments-importer.zip` to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a Facebook APP at developers.facebook.com/apps/
4. Configure the plugin by going to the `FB Comments Importer` page

== Frequently asked questions ==

== Screenshots ==

1. FB Comments Importer FREE
2. FB Comments Importer PRO

== Changelog ==

= 1.6.1 2015-02-12 =
* fixed bug related with short links auto follow

= 1.6 2015-02-10 =
* Performance improvement
* Ability to automatically follow redirects and connect short URLs (goo.gl, tinyurl.com, tiny.cc etc.)
* file_get_content removed from all functions, and replaced with curl

= 1.5.2 2015-02-06 =
* Fixed error handler for wrong facebook page ID
* Few notices from error reporting fixed

= 1.5.1 2015-01-30 =
* Some notices from error reporting fixed
* Small template changes
* Few small bug fixes in FB Comments class 

= 1.5 2015-01-26 =
* Light interface redesign
* get_avatar filter priority changed

= 1.4.1 2014-04-10 =
* Avatar bug fixed. Facebook API has changed so we had to do it.

= 1.4 2014-04-10 =
* NEW FEATURE: now you can change status for imported comments (pending or approved)
* Few bugs fixed (number of imported comments was wrong, limit in form and etc...)
* Admin interface improvements

= 1.3.3 2013-01-17 =
* Fixed another bug with duplicate comments being imported
* Better compatibility with Wordpress 3.8

= 1.3.2 2013-09-19 =
* Improved duplicate comments detection.
* Fixed bug with user avatars not being displayed.
* Removed the ugly array warning message when trying to import post without comments.
* Added a screenshot for free and pro versions

= 1.3.1 2013-08-31 =
* Fixed bug with importing duplicate comments in posts with the non-standard Facebook emoticons.
* Fixed some warning in_array errors.

= 1.3 2013-08-15 =
* Plugin now imports the Facebook comment replies as well as regular comments.
* Expanded the limit of posts available for import to 50. For more you need to purchase a PRO version.
* Added the CURL check, so the plugin reports if you don't have it enabled on your server.
* Added the "Show / Hide unavailable posts" button.
* Comments count is now retrieved about twice times faster.
* Fixed several bugs which usually led to a blank page or different PHP errors.

= 1.0.2 2013-03-10 =
* Plugin didnt work on some server configurations with short_tags set to off in php.ini

= 1.0.1 2013-03-02 =
* Manual import was giving some errors on some server configurations.

= 1.0 2013-03-01 =
* Initial release