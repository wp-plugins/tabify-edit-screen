<?php
/*
Plugin Name: Tabify edit screen
Plugin URI: http://wp-rockstars.com/plugin/tabify-edit-screen
Description: Enables tabs in the edit screen and manage them from the back-end
Author: Marko Heijnen
Version: 0.2
Author URI: http://markoheijnen.com
*/

include 'inc/admin.php';
include 'inc/tabs.php';

class Tabify_Edit_Screen {
	private $admin;
	private $editscreen_tabs;

	function __construct() {
		$admin = new Tabify_Edit_Screen_Admin();

		add_action( 'admin_menu', array( &$admin, 'admin_menu' ) );

		add_filter( 'redirect_post_location', array( &$this, 'redirect_add_current_tab' ), 10, 2 );

		add_action( 'admin_print_scripts-post.php', array( &$this, 'show_tabs' ), 1000 );
		add_action( 'admin_print_scripts-post-new.php', array( &$this, 'show_tabs' ), 1000 );
	}

	function redirect_add_current_tab( $location, $post_id ) {
		if( isset( $_REQUEST['tab'] ) ) {
			$location =  add_query_arg( 'tab', $_REQUEST['tab'], $location );
		}
		return $location;
	}

	function show_tabs() {
		global $post_type;

		$options = get_option( 'tabify-edit-screen', array() );

		if( isset( $options[ $post_type ], $options[ $post_type ]['show'] ) && $options[ $post_type ]['show'] == 1 ) {
			$this->editscreen_tabs = new Tabify_Edit_Screen_Tabs( $options[ $post_type ]['tabs'] );
			$default_metaboxes = $this->editscreen_tabs->get_default_metaboxes( $post_type );

			add_action( 'admin_print_footer_scripts', array( &$this, 'generate_javascript' ), 9 );
			add_action( 'dbx_post_sidebar', array( &$this, 'add_form_inputfield' ) );

			foreach( $options[ $post_type ]['tabs'] as $tab_index => $tab ) {
				$class = 'tabifybox tabifybox-' . $tab_index;

				if( $this->editscreen_tabs->get_current_tab() != $tab_index ) {
					$class .= ' tabifybox-hide';
				}

				foreach( $tab['metaboxes'] as $metabox_id_fallback => $metabox_id ) {
					if( intval( $metabox_id_fallback ) == 0 && $metabox_id_fallback !== 0 ) {
						$metabox_id = $metabox_id_fallback;
					}

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
			$tabs = $this->editscreen_tabs->get_tabs_with_container( false );

			echo '<script type="text/javascript">';
			echo 'jQuery(function($) {';
			//echo '$( ".wrap > h2" ).addClass( "nav-tab-wrapper" );';
			//echo '$( ".wrap > h2" ).append(' . $tabs . ');';

			echo '$("#post").before( \'' . $tabs . '\' );';
			//echo '$("#post").prepend( \'' . $this->editscreen_tabs->get_tabs_current_tab_input() . '\' );';
			echo '});';

			do_action( 'tabify_custom_javascript' );
			echo '</script>';
		}
	}

	function add_form_inputfield() {
		echo $this->editscreen_tabs->get_tabs_current_tab_input();
	}
}


new Tabify_Edit_Screen();