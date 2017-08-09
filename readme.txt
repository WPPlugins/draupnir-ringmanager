=== Draupnir Ringmanager ===
Contributors: jarandhel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X4UZU2MRD7N4U
Tags: webring, webrings, blogring, blogrings, ringmaker, ring management, ring, rings, links, linking
Requires at least: 3.6.0
Tested up to: 4.0
Stable tag: trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows for the creation and management of a webring, and the display of webring code in posts, pages, or a widget.

== Description ==

This plugin allows for the creation and management of a webring, and the display of webring code in posts, pages, or a widget.  These functions may be used together or separately.

= Features: =
* Create your own webring - no third party provider needed!
* Style your webring code however you like it.  Basic html, image maps, css, it's all up to you.
* Sites that are not currently accessible will be seamlessly skipped in normal ring navigation.
* Ring order can be randomized manually or on a set schedule, or edited by hand.
* Ring members can update their sites url and description at any time, or choose to leave the ring.
* New members will be emailed the appropriate css code to add to their site.
* One user login can be the owner of multiple sites! Even the ring owner!
* The status of the code on each ring member's site is checked on a periodic basis and displayed in the ring management area.
* ... and more!

== Installation ==


1. Install Draupnir either via the WordPress.org plugin directory, or by uploading the files to your server
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Set up Draupnir using the menu found in your admin sidebar.
	* Pick a name for your webring.
	* Write a basic description of your webring.
	* Select an image for your webring.
	* Set a homepage for your webring.
1. Create a page for your webring.  
	* This page should have the same url as the homepage you selected earlier. 
	* It's recommended that you give it a title that matches the name you have chosen for your ring.  
	* The only contents it needs to have is the shortcode [draupnir_ringhub].
1. Display the webrings of which you are a member on your site.
	* Fill in your site's individual ring code under Code for All Webrings.
	* Display these webrings on any post or page using the shortcode [draupnir_ringcodes]
	* Or use the provided Webrings widget.

== Frequently Asked Questions ==

= How much can I customize my ring code or css? =

That's really up to you and your skills with html, css, javascript, and other web technologies.  On one of the webrings I manage using Draupnir, I've switched from the default code to using an imagemap for all ring navigation.  As long as the links are correct, that's what really matters.

= What if I mess up my ring code or css? =

To reset your current ring code or css (including color choices) to the defaults, just clear the fields and click on update options.  The code for this webring, ringheader color, ringlisting color, ringdescription color, and css for this website fields will be repopulated with their defaults any time they are blank when the options are updated.

= What's the deal with the donation link? =

Draupnir Ringmanager is donationware.  It's entirely free, and always will be, and its source code is available for anyone to modify freely however they would like. There will never be limits on its functionality, ads of any kind, or a paid version with additional features.  Instead, if you like it and have some funds to spare, please consider making a small donation to show your appreciation.  Thank you.

= Webrings? Seriously? =

I know what you're thinking - webrings, in this day and age?  Surely that died out back when yahoo took over webring.com?  And you're right, it is hard to find a decent webring provider these days.  But the basic problem remains -- how do you link together groups of topically related sites so that visitors can easily find them?  Link exchanges are one method, but it's complicated to get every site to link to every other site.  Blogrolls are another, but above a certain size they grow cumbersome and again it's hard to get every blog to link to every other one.  To my knowledge, there is still no better method than a webring for really connecting a community of interrelated sites.  So why not host your own, without the middle men?

== Screenshots ==

1. General options.
2. Webring appearance options.
3. Webring email options.
4. Managing all ring sites as a ring owner.
5. The Webring widget.
6. Viewing the webring hub.

== Changelog ==

= 1.7.7 =
* Minor bugfix for the stats feature.  It was reporting all-time statistics rather than 8 week statistics when the stats for the full ring were viewed due to a malformed MSQL query.

= 1.7.6 =
* Minor bugfix to allow the admin to remove 'override' status for checking ring code on a site.
* Added public stats.

= 1.7.5 =
* Security update: rewrote code to better protect against SQL injection.
* Placed plugin functions inside their own class to reduce chance of conflicts with other plugin/theme functions.

= 1.7.4 =
* Updated Draupnir code to use a more efficient options system.  Older options should be converted seamlessly.  This dramatically reduces the number of database queries needed by Draupnir.
* Updated stats feature to display hits and clicks separately.
* Updated ring reordering feature to allow for reordering based on the site's ring activity.

= 1.7.3 =
* Final fix for stats table not being created - automatic update does not call the plugin activation hook despite deactivating and reactivating the plugin.  Now plugin checks for a stored version number and if the version number does not match the update functions are called and the stored version number is updated.
* Replaced deprecated register_sidebar_widget function, replaced with wp_register_sidebar_widget.  You will probably have to re-add your webring widget to your sidebar if upgrading.
* Added links from ring management to show the statistics for each page.
* Added a top 10 listing for sites receiving hits through the ring to the stats page.


= 1.7.2 =
* Important update - new stats table was not being properly created on plugin update.

= 1.7.1 =
* Minor update - added some bare bones statistics functionality.  Should provide the basis for periodically reordering the webring according to least or most visited sites in the future.

= 1.7 =
* Added pagination for larger rings, and the ability for users to select how many sites to display at one time.
* Added ability for ring manager to set individual sites code check status to "override".
* Updated FAQ.

= 1.6 =
* Added option to randomize ring order on a periodic basis (daily, weekly, fortnight, monthly).
* Added lookahead capability to listing of ring sites on ring homepage.  If a site is not available the visitor will be returned to the hub and an error displayed.

= 1.5.1 =
* Fixed bug that added Draupnir settings, support, and donate links to every plugin listed on the plugins page - sorry folks, did not notice that till now.

= 1.5 =
* Added link to ring hub (frontend) on ring management (backend) so that ring owners can more easily check their ring after making setting changes.
* Added check for ring code to ring management.  Does not check for exact code, just that all ring links are present on the page in some form with the proper ids attached.  Includes logic to check for &, &amp;amp;, and &amp;#38; in the links.
* Added cron task checking one site for ring code each hour, selecting the one that was checked least recently.
* Added option for manual check.
* Added lookahead feature to check that the next site in the ring (on next, prev, and rand) is available before forwarding.  If not, the ring will skip to a site that is available.

= 1.4.1 =
* Bugfix for ring order management.

= 1.4 =
* Added link to ring management (backend) on ring hub page (frontend) for logged-in ring owner.
* Added ability for sites to specify a url other than their main page to contain ring code. (For sites with a separate page for webrings, or a splash page.)
* Added the date a site joined the ring to the info displayed in ring management.
* Added ability to shuffle the order of sites in the webring from ring management.
* Added ability to reorder sites from backend.

= 1.3.1 =
* Bugfix: Ring management did not strip escaping slashes in site names before display.
* Updated FAQ & Readme to accurately state the shortcode for displaying rings as [draupnir_ringcodes] rather than [draupnir_ringsdisplay] which was actually the function name.

= 1.3 =

* Updated the FAQ.
* Added capability for ring owner to add sites from the backend.
* Added Settings and Plugin Support links to the page listing installed plugins.
* Added the capability to customize the emails sent by Draupnir to notify the ring owner of new sites, and to welcome new members to the ring.

= 1.2 =
* Bugfix for wrong colspan in ring listing when visitor does not own any ring sites.

= 1.1 =
* Moved ring management functionality to backend.
* Added styling options & FAQ to admin menus.
* Made everything look nicer.
* Added defaults for ring code & styling.
* Misc. bug fixes & optimizations.

= 1.0 =
* First release of Draupnir.  Everything's new!

== Upgrade Notice ==

= 1.7.7 =
Minor bugfix for the stats feature.

= 1.7.6 =
New function: Public stats! Also minor bugfix to navcode override functionality. And spiffiness.

= 1.7.5 =
Security update and improved compatibility with other plugins.  Please update.

= 1.7.4 =
New feature: reorder by ring activity.  Also upgrades to plugin options, please update.

= 1.7.3 =
More important bugfixes, new stats features, please update.

= 1.7.2 =
Important bugfix, please update.

= 1.7.1 =
New feature: Barebones ring statistics & a smidgen more spiffiness.

= 1.7 =
New features: Pagination & Code Check Override! And still more spiffiness.

= 1.6 =
New features & an extra dose of all-around spiffiness!

= 1.5.1 =
Important bugfix, please update.

= 1.5 =
Various new features and 300% more spiffiness!

= 1.4.1 =
Small but necessary bugfix for ring order management.

= 1.4 =
New features, UI improvements, and still more all around spiffiness.

= 1.3 =
FAQ Update, new features, and more all around spiffiness.

= 1.2 =
Fixed formatting bug in ring listing for visitors who do not own ring sites.

= 1.1 =
New features, better styling, bug fixes, and all around spiffiness.
