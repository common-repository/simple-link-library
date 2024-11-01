=== WordPress Simple Link Library  ===
Contributors: MaikelM
Tags: links, library
Stable tag: 1.3.4
Requires at least: 4.0
Tested up to: 6.6
License: GPLv3 or higher

Manage your link collection in a simple way.

== Description ==
Manage and display links on a WordPress site. Plugin uses custom types, so exporting is very easy.
To display links in a post or page use the following short tags:

[links cat="name of category"]

[SHOWLINKS] for displaying all links

A simple option to export links per category is possible using the overview screen:
Click "All Link Items" -> Click on the link Category you want to export -> [OPTIONAL, but recommended] Select "Title" to alphabetize your export list -> Select "Bulk actions" and chose the Export to xHTML -> Click "Apply" Now you can save you selection. Use pandoc.org to convert your selection to markdown or another format if needed.

This Plugin has a "broken link" check functionality. 
The Broken checks can take a while with a significant number of links. The screen stays blanc during progress. note: No progress bar is shown. 



See https://www.bm-support.org/innovation-links/ for a live demo.


How does it work?
This module makes use of Custom Post Types with non hierarchical catalogue option. So you can give links one or multiple tags.
Tags works great for retrieving or exporting only certain types of links.
If all tags for a link are deleted, the link still exist. This is default WP functionality. Since this plugin in built on using default wordpress hooks,
exporting and importing links can be done used as with post or pages. 


== Installation ==
Follow this steps to install this plugin:

1. Download the plugin into the **/wp-content/plugins/** folder
2. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==
1. Basic output with [SHOWLINKS]  tag.

== Changelog ==

= 1.3.4 =
* Tested with WordPress 6.6.2

= 1.3.3 =
* Tested with WordPress 6.2.2

= 1.3.2 =
* Tested with WordPress 5.6

= 1.3.1 =
* Tested with WordPress 5.5
* Export per category possible 

= 1.2.1 =
* Improved xHTML output for export
* Tested with WP5.3 

= 1.1.2 =
* Tested with WP5.3 


= 1.1.1 =
* Tested with WP5.0.2
* Tested and valided correct working with Gutenburg shortcode BLOCK


= 1.1.0 =
* Tested with WP4.9.7
* Added this CPT plugin for exporting using WP-API
* Created plain xHTML export function 
* Adjusted help text


= 1.0.1 =

* Tested with WP4.5
* Minor security update to prevent ajax hacking on input
* Tested with WP 4.3

= 1.0.2 =
* Tested with WP4.6

= 1.0.3 =
* Tested with WP4.7.2

= 1.0.4 =
* Tested with WP4.8.2

= 1.0.5 =
* Tested with WP4.9.5
* Updated code, to fix issue 'Fixing no space before parentheses' , see: https://wordpress.org/support/topic/fixing-no-space-before-parenthesis/


