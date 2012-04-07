=== Tabify Edit Screen ===
Contributors: markoheijnen
Donate link: http://wp-rockstars.com/plugins/tabify-edit-screen/
Tags: tabs, edit, admin, post, page, posts
Requires at least: 3.3
Tested up to: 3.4
Stable tag: 0.3

Enable tabs in the edit screen and manage them from the back-end.

== Description ==

When you've got lots of post meta-boxes, your post edit screen can become difficult to search. Make your post edit screen easier to navigate and manage by creating a set of tabs, with the Tabify Edit Screen plugin. And manage it all from the WordPress back-end!

The plugin is still in active development, and I'm always looking for ways to improve it. 

Known issue: currently the plugin ignores the "show on screen" values from the Screen options. I'm currently working to fix this issue.


== Installation ==

1. Upload the folder `tabify-edit-screen` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Settings -> Tabify edit screen to enable tabs for a specific post type

== Frequently Asked Questions ==

= Can you define metaboxes that will always be visible? =

At this moment the title and submit box are always visible. You can change this by using the following filters:
 * tabify_default_metaboxes - $defaults, $post_type
 * tabify_default_metaboxes_$post_type - $defaults

== Screenshots ==

1. How it would look like after enabling this plugin
1. The settings page where you can manage the tabs for each posttype

== Changelog ==

= 0.3 (2012-4-7) =
* Add support for the plugin "Types"
* Removed the filter tabify_default_metaboxes_$posttype and changed it for the action tabify_add_meta_boxes
* Improved the way to delete a tab by showing a remove link ( Javascript only )
* Make the tab title translatable with tabify-edit-screen-tab as domain
* The text in javascript is now also translatable
* Removed duplicated code

= 0.2 (2012-4-6) =
* Added security checks on the setting screen
* Create a new tab now also works on all post types
* When you save the changes you go back to the selected tab. This also work for the edit screens
* Setting page works when javascript isn't supported
* You can now delete a tab when all the metaboxes are removed and the title is empty. Will be improved in later version
* New metaboxes will always be showed in the setting page
* The setting page now can be changed from a touch device
* Fix issue where you couldn't move metaboxes in a new created tab

= 0.1 (2012-4-2) =
* First version to show people the possibilities

== TODO's ==

* Better UI admin
* Know when a metabox is showed from the screen options
* Let user be able to move meta boxes to a different tab
* Improve deleting a tab from the setting screen
* The sorting of metabox in the setting should be listen to in the edit screen