=== Plugin Name ===
Contributors: markoheijnen
Donate link: http://wp-rockstars.com/plugins/tabify-edit-screen/
Tags: tabs, edit, admin, post, page
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 0.1

Enables tabs in the edit screen and manage them from the back-end

== Description ==

You ever came in a situation where the edit screen of a post type get to full that you sometimes had to search the right metabox?
This plugin enables tabs in the edit screen and manage them from the back-end.

It's still in development. In general the plugin does the things it should do but there still are a lot of things that can be improved.
Also at this moment it ignores the "Show on screen" values from the Screen options. What can be a big thing for some users

== Installation ==

1. Upload the folder `tabify-edit-screen` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Settings -> Tabify edit screen to enable tabs for a specific post type

== Frequently Asked Questions ==

= Can you define metaboxes that will always be visible =

At this moment the title and submit box are always visible. You can change this by using the following filters:
 * tabify_default_metaboxes - $defaults, $post_type
 * tabify_default_metaboxes_$post_type - $defaults

== Screenshots ==

1. How it would look like after enabling this plugin

== Changelog ==

= 0.2 (2012-4-6) =
* Added security checks on the setting screen
* Create new tab now also works on all post types
* When you save the changes go back to the selected tab
* Setting page works when javascript isn't supported (need fix)
* You can now delete a tab when all the metaboxes are removed and the title is empty. Will be improved in later version

= 0.1 (2012-4-2) =
* First version to show people the possibilities

== TODO's ==

* Better UI admin
* Know when a metabox is showed from the screen options
* Let user be able to move meta boxes to a different tab
* Improve deleting a tab from the setting screen
* Make the setting page work when the user is on a touch screen
* Let is work with types (some how the values are empty)