=== Performance Utilities ===
Contributors: jruns
Tags: performance, optimization, speed, delay, lcp
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Improve the performance of your WordPress website.

== Description ==

This plugin includes several utilities that can be activated and configured in order to improve various aspects of your website.

**Utilities included:**  
- **Disable jQuery Migrate** on the frontend.
- **Remove versions** from external script and style source urls.
- **Enable YouTube Facade**: Replace YouTube iframes with a placeholder image and delay loading videos until the user clicks on the placeholder image.
- **Preload Images**: Preload specified images on specific pages and at specified viewport sizes to improve Largest Contentful Paint (LCP).
- **Move Scripts and Styles to the footer**: Move specified scripts and styles to the page footer on the frontend on specific pages.
- **Remove Scripts and Styles**: Remove specified scripts and styles from the frontend on specific pages.
- **Delay Scripts and Styles**: Delay execution of specified scripts and styles until the page has loaded or the user has interacted with the page.

[Visit our wiki](https://github.com/jruns/wp-performance-utilities/wiki) to learn how to configure the plugin and use each utility.

== Installation ==

From your WordPress dashboard

1. Visit Plugins > Add New
2. Search for "Performance Utilities"
3. Install and Activate _Performance Utilities_ from your Plugins page
4. Visit _Settings_ > _Performance Utilities_ to enable utilities
5. Implement utility-specific WordPress filters in your theme's functions.php file, if necessary

== Screenshots ==

1. Admin Settings Page
2. Preload Images utility's post meta box

== Frequently Asked Questions ==

= Why is this free? =

Because websites should be fast without costing money.

= How do I configure the utilities? =

[Visit our wiki](https://github.com/jruns/wp-performance-utilities/wiki) to learn how to configure the plugin and use each utility.

== Changelog ==

= 1.0.0 =
* Initial plugin repository release with several performance utilities.