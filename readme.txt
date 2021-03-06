=== Page Siblings ===
Contributors: iamntz
Tags: page administration, utils, custom post type
Requires at least: 3.0
Tested up to: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A metabox with all page edit (and any other hierarchal post types) that display an edit link to its siblings.

== Description ==

Ever had to manage a WP install with many pages and subpages? This plugin does nothing more than adding
a metabox to every post type that is hierarchical (that is pages and any other custom post type!) with 
a list of all page siblings, starting with the parent, so you can have:

`
News
|— History
|— Our Staff
|—— Employment Opportunities
|— Our Company
`

== Installation ==

1. Upload the `page-siblings` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Done. You will see a box with page siblings when you edit a page

== Screenshots ==
1. Page Siblings metabox

== Changelog ==

= 1.0 =
* Initial version

= 1.0.1 =
* added a filtering option to page editing, so you can only display parent pages

= 1.0.2 =
* tweaked code a little & removed PHP notices & warnings

= 1.0.3 =
* Updated readme

= 1.0.4 =
* added correct sorting for pages (thanks Jakob for pointing this)