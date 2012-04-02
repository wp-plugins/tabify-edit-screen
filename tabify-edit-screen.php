<?php
/*
Plugin Name: Tabify edit screen
Plugin URI: http://wp-rockstars.com/plugin/tabify-edit-screen
Description: Enables tabs in the edit screen and manage them from the back-end
Author: Marko Heijnen
Version: 0.1
Author URI: http://markoheijnen.com
*/

/*
 * TODO
 * - Better UI admin
 * - Know when a metabox is disabled/enabled
 * - Let user be able to move meta boxes to a different tab
 * 
 */

include 'inc/admin.php';
include 'inc/tabs.php';

class Tabify_Edit_Screen {
	private $admin;
	private $editscreen_tabs;

	function __construct() {
		$admin = new Tabify_Edit_Screen_Admin();

		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$admin, 'admin_menu' ) );

		add_action( 'admin_print_scripts-post.php', array( &$this, 'show_tabs' ), 1000 );
		add_action( 'admin_print_scripts-post-new.php', array( &$this, 'show_tabs' ), 1000 );
	}

	function admin_init() {
		
	}

	function show_tabs() {
		global $post_type;

		$options = get_option( 'tabify-edit-screen', array() );
		$this->editscreen_tabs = new Tabify_Edit_Screen_Tabs( $options[ $post_type ]['tabs'] );
		$default_metaboxes = $this->editscreen_tabs->get_default_metaboxes();

		if( isset( $options[ $post_type ], $options[ $post_type ]['show'] ) && $options[ $post_type ]['show'] == 1 ) {
			add_action( 'admin_print_footer_scripts', array( &$this, 'generate_javascript' ), 9 );

			foreach( $options[ $post_type ]['tabs'] as $tab_index => $tab ) {
				$class = 'tabifybox tabifybox-' . $tab_index;

				if( $this->editscreen_tabs->get_current_tab() != $tab_index ) {
					$class .= ' tabifybox-hide';
				}

				foreach( $tab['metaboxes'] as $metabox_id => $metabox_title ) {
					if( ! in_array( $metabox_id, $default_metaboxes ) ) {
						if( $metabox_id == 'titlediv' || $metabox_id == 'postdivrich' ) {
							$func = create_function('', 'echo "jQuery(\"#' . $metabox_id . '\").addClass(\"' . $class . '\");";');
							add_filter( 'tabify_custom_javascript' , $func );
						}
						else {
							$func = create_function('$args', 'array_push($args, "' . $class . '"); return $args;');
							add_filter( 'postbox_classes_' . $post_type . '_' . $metabox_id, $func );
						}
					}
				}
			}
		}
	}

	function generate_javascript() {
		global $post_type;

		$options = get_option( 'tabify-edit-screen', array() );

		if( isset( $options[ $post_type ], $options[ $post_type ]['show'] ) && $options[ $post_type ]['show'] == 1 ) {
			$tabs = $this->editscreen_tabs->get_tabs_with_container();

			echo '<script type="text/javascript">';
			echo 'jQuery(function($) {';
			//echo '$( ".wrap > h2" ).addClass( "nav-tab-wrapper" );';
			//echo '$( ".wrap > h2" ).append(' . $tabs . ');';

			echo '$("#post").before( \'' . $tabs . '</h2>\' );';
			echo '});';

			do_action( 'tabify_custom_javascript' );
			echo '</script>';
		}
	}
}


new Tabify_Edit_Screen();