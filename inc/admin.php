<?php

class Tabify_Edit_Screen_Admin {
	private $metaboxes = array();
	private $tabs;

	/**
	 * Adds a option page
	 *
	 * @since 0.1
	 */
	public function admin_menu() {
		add_options_page( __( 'Tabify edit screen', 'tabify-edit-screen' ), __( 'Tabify edit screen', 'tabify-edit-screen' ), 'manage_options', 'tabify-edit-screen', array( &$this, 'edit_screen' ) );
	}

	/**
	 * Option page that handles the form request
	 *
	 * @since 0.1
	 */
	public function edit_screen() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		}
		
		$this->update_settings();

		wp_register_script( 'tabify-edit-screen-admin', plugins_url( '/js/admin.js', dirname( __FILE__ ) ), array( 'jquery', 'jquery-ui-sortable' ), '1.0' );
		wp_enqueue_script( 'tabify-edit-screen-admin' );

		echo '<div class="wrap">';

		screen_icon();
		echo '<h2>' . esc_html( get_admin_page_title() ) . '</h2>';

		echo '<form method="post">';
		wp_nonce_field( plugin_basename( __FILE__ ), 'tabify_edit_screen_nonce' );

		$posttypes = $this->get_posttypes();
		$this->get_tabs( $posttypes );
		$this->get_metaboxes( $posttypes );

		echo '</form>';

		echo '</div>';
	}

	/**
	 * Updates settings
	 *
	 * @since 0.2
	 *
	 */

	private function update_settings() {
		if( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_POST['tabify'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'tabify_edit_screen_nonce' ) ) {
			$options = $_POST['tabify'];
			$this->escape( $options );

			update_option( 'tabify-edit-screen', $options );
		}
	}

	/**
	 * Sanitize string or array of strings for database.
	 *
	 * @since 0.2
	 *
	 * @param string|array $array Sanitize single string or array of strings.
	 * @return string|array Type matches $array and sanitized for the database.
	 */
	private function escape( &$array ) {
		global $wpdb;

		if ( ! is_array( $array ) ) {
			return esc_attr( wp_strip_all_tags( $array ) );
		}
		else {
			foreach ( (array) $array as $k => $v ) {
				if ( is_array( $v ) ) {
					$this->escape( $array[ $k ] );
				}
				else {
					$array[$k] = esc_attr( wp_strip_all_tags( $v ) );
				}
			}
		}
	}

	/**
	 * Gets all the post types
	 *
	 * @since 0.1
	 *
	 * @return array All post types that are showed from the backend.
	 */
	private function get_posttypes() {
		$args = array(
			'show_ui' => 'true'
		);

		$posttypes_objects = get_post_types( $args, 'objects' );
		$posttypes_objects = apply_filters( 'tabify_posttypes', $posttypes_objects );

		$posttypes = array();
		foreach( $posttypes_objects as $posttype_object ) {
			$posttypes[ $posttype_object->name ] = $posttype_object->label;
		}

		return $posttypes;
	}

	/**
	 * Echo the tabs for the settings page
	 *
	 * @since 0.1
	 */
	private function get_tabs( $posttypes ) {
		$this->tabs = new Tabify_Edit_Screen_Tabs( $posttypes );
		echo $this->tabs->get_tabs_with_container();
	}

	/**
	 * Echo all the metaboxes
	 *
	 * @since 0.1
	 */
	private function get_metaboxes( $posttypes ) {
		$metaboxes = $this->initialize_metaboxes( $posttypes );
		$options = get_option( 'tabify-edit-screen', array() );

		$default_metaboxes = $this->tabs->get_default_metaboxes();

		foreach( $posttypes as $name => $label ) {
			if( !isset( $options[ $name ] ) ) {
				$options[ $name ] = array (
					'tabs' => array(
						array( 'title' => 'Others', 'metaboxes' => $metaboxes[ $name ] )
					)
				);
			}

			if( $name == $this->tabs->get_current_tab() ) {
				echo '<div class="tabifybox tabifybox-' . $name . '">';
			}
			else {
				echo '<div class="tabifybox tabifybox-hide tabifybox-' . $name . '">';
			}

			$checked = '';
			if( isset( $options[ $name ]['show'] ) && $options[ $name ]['show'] == 1 ) {
				$checked = ' checked="checked"';
			}

			echo '<div class="tabifybox-options">';
			echo '<p><input type="checkbox" name="tabify[' . $name . '][show]" value="1" ' . $checked . '/> ' . __( 'Show tabs in this post type.', 'tabify-edit-screen' ) . '</p>';
			echo '</div>';

			echo '<div class="tabify_control">';

			$i = 0;
			foreach( $options[ $name ]['tabs'] as $tab ) {
				echo '<div>';
				echo '<h2><span class="tabify-title">' . $tab['title'] . '</span><input type="text" name="tabify[' . $name . '][tabs][' . $i . '][title]" value="' . $tab['title'] . '" class="tabify-title-input" /></h2>';
				echo '<ul style="margin: 0px; padding: 6px 0px 0px;">';
				if( isset( $tab['metaboxes'] ) ) {
					foreach( $tab['metaboxes'] as $metabox_id => $metabox_title ) {
						$class = 'menu-item-handle';

						if( in_array( $metabox_id, $default_metaboxes ) ) {
							$class = ' tabifybox-hide';
						}

						echo '<li class="' . $class . '">' . $metabox_title;
						echo '<input type="hidden" name="tabify[' . $name . '][tabs][' . $i . '][metaboxes][' . $metabox_id  . ']" value="' . $metabox_title . '" />';
						echo '</li>';

						unset( $metaboxes[ $name ][ $metabox_id ] );
					}

					if( count( $options[ $name ]['tabs'] ) == ( $i + 1 ) ) {
						foreach(  $metaboxes[ $name ] as $metabox_id => $metabox_title ) {
							$class = 'menu-item-handle';
	
							if( in_array( $metabox_id, $default_metaboxes ) ) {
								$class = ' tabifybox-hide';
							}
	
							echo '<li class="' . $class . '">' . $metabox_title;
							echo '<input type="hidden" name="tabify[' . $name . '][tabs][' . $i . '][metaboxes][' . $metabox_id  . ']" value="' . $metabox_title . '" />';
							echo '</li>';
						}
					}
				}
				echo '</ul>';
				echo '</div>';

				$i++;
			}


			echo '</div>';

			echo '</div>';
		}

		echo '<p class="submit">';
		echo '<input type="button" id="create_tab" class="button-secondary" value="' . __( 'Create a new tab', 'tabify_edit_screen' ) . '" />';
		submit_button( '', 'primary', 'submit', false );
		echo '</p>';
	}

	/**
	 * Gets all the metaboxes that are registered
	 *
	 * @since 0.1
	 */
	private function initialize_metaboxes( $posttypes ) {
		if( ! $this->metaboxes ) {
			global $wp_meta_boxes;

			foreach( $posttypes as $posttype => $label ) {
				$this->metaboxes[ $posttype ] = array();

				if ( post_type_supports( $posttype, 'title' ) ) {
					$this->metaboxes[ $posttype ][ 'titlediv'] = __( 'Title' );
				}

				if ( post_type_supports( $posttype, 'editor' ) ) {
					$this->metaboxes[ $posttype ][ 'postdivrich'] = __( 'Editor' );
				}

				$this->load_default_metaboxes( $posttype );
				do_action( 'add_meta_boxes', $posttype, null );
				do_action( 'add_meta_boxes_' . $posttype, null );
			}

			foreach( $wp_meta_boxes as $posttype => $context ) {
				foreach( $context as $priorities ) {
					foreach( $priorities as $priority => $metaboxes ) {
						foreach( $metaboxes as $metabox ) {
							$this->metaboxes[ $posttype ][ $metabox['id'] ] = $metabox['title'];
						}
					}
				}
			}
		}

		return $this->metaboxes;
	}

	/**
	 * Gets all the default WordPress metaboxes
	 * Little bit hackish but it works. Hopefully one day there will be a method for this in core.
	 *
	 * @since 0.1
	 */
	private function load_default_metaboxes( $post_type ) {
		add_meta_box( 'submitdiv', __('Publish'), 'post_submit_meta_box', $post_type, 'side', 'core' );

		if ( current_theme_supports( 'post-formats' ) && post_type_supports( $post_type, 'post-formats' ) )
			add_meta_box( 'formatdiv', _x( 'Format', 'post format' ), 'post_format_meta_box', $post_type, 'side', 'core' );

		// all taxonomies
		foreach ( get_object_taxonomies($post_type) as $tax_name ) {
			$taxonomy = get_taxonomy($tax_name);
			if ( ! $taxonomy->show_ui )
				continue;

			$label = $taxonomy->labels->name;

			if ( !is_taxonomy_hierarchical($tax_name) )
				add_meta_box('tagsdiv-' . $tax_name, $label, 'post_tags_meta_box', $post_type, 'side', 'core', array( 'taxonomy' => $tax_name ));
			else
				add_meta_box($tax_name . 'div', $label, 'post_categories_meta_box', $post_type, 'side', 'core', array( 'taxonomy' => $tax_name ));
		}

		if ( post_type_supports($post_type, 'page-attributes') )
			add_meta_box('pageparentdiv', 'page' == $post_type ? __('Page Attributes') : __('Attributes'), 'page_attributes_meta_box', $post_type, 'side', 'core');

		if ( current_theme_supports( 'post-thumbnails', $post_type ) && post_type_supports( $post_type, 'thumbnail' ) )
				add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', $post_type, 'side', 'low');

		if ( post_type_supports($post_type, 'excerpt') )
			add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', $post_type, 'normal', 'core');

		if ( post_type_supports($post_type, 'trackbacks') )
			add_meta_box('trackbacksdiv', __('Send Trackbacks'), 'post_trackback_meta_box', $post_type, 'normal', 'core');

		if ( post_type_supports($post_type, 'custom-fields') )
			add_meta_box('postcustom', __('Custom Fields'), 'post_custom_meta_box', $post_type, 'normal', 'core');

		do_action('dbx_post_advanced');
		if ( post_type_supports($post_type, 'comments') )
			add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', $post_type, 'normal', 'core');

		if ( post_type_supports($post_type, 'comments') )
			add_meta_box('commentsdiv', __('Comments'), 'post_comment_meta_box', $post_type, 'normal', 'core');

		add_meta_box('slugdiv', __('Slug'), 'post_slug_meta_box', $post_type, 'normal', 'core');

		if ( post_type_supports($post_type, 'author') ) {
			if ( is_super_admin() || current_user_can( $post_type_object->cap->edit_others_posts ) )
				add_meta_box('authordiv', __('Author'), 'post_author_meta_box', $post_type, 'normal', 'core');
		}

		if ( post_type_supports($post_type, 'revisions') )
			add_meta_box('revisionsdiv', __('Revisions'), 'post_revisions_meta_box', $post_type, 'normal', 'core');
	}
}