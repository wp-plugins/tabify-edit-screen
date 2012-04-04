<?php

class Tabify_Edit_Screen_Tabs {
	private $base_url = '';

	private $active = '';
	private $items = array();

	function __construct( $items ) {
		if( is_array( $items ) ) {
			$this->items = $items;

			if( isset( $_REQUEST['tab'] ) ) {
				$this->active = esc_attr( $_REQUEST['tab'] );
			}

			$this->base_url = remove_query_arg( 'tab', $_SERVER["REQUEST_URI"] );

			if( empty( $this->active ) || !isset( $items[ $this->active ] ) ) {
				$this->active = key( $items );
			}

			return true;
		}
		return false;
	}

	public function get_current_tab() {
		return $this->active;
	}

	public function get_tabs_with_container() {
		$return  = '<h2 class="nav-tab-wrapper" style="padding-left: 20px;">';
		$return .= '<input type="hidden" id="current_tab" name="tab" value="' . $this->active. '" />';
		$return .= $this->get_tabs();
		$return .=  '</h2>';

		return $return;
	}
	
	public function get_tabs() {
		$return = '';

		foreach( $this->items as $key => $title ) {
			if( is_array( $title ) ) {
				$title = $title['title'];
			}

			if( $this->active == $key ) {
				$return .= '<a id="tab-' . $key . '" href="' . $this->base_url . '&tab=' . $key . '" class="tabify-tab nav-tab nav-tab-active">' . $title . '</a>';
			}
			else {
				$return .= '<a id="tab-' . $key . '" href="' . $this->base_url . '&tab=' . $key . '" class="tabify-tab nav-tab">' . $title . '</a>';
			}
		}

		//When tabs are requested also enqueue the javascript and css code
		wp_register_script( 'tabify-edit-screen', plugins_url( '/js/tabs.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0' );
		wp_enqueue_script( 'tabify-edit-screen' );

		wp_register_style( 'tabify-edit-screen', plugins_url( '/css/tabs.css', dirname( __FILE__ ) ), array( ), '1.0' );
		wp_enqueue_style( 'tabify-edit-screen' );

		return $return;
	}

	public function get_default_metaboxes( $post_type = '' ) {
		$defaults = array( 'titlediv', 'submitdiv' ); //, 'postdivrich'
		$defaults = apply_filters( 'tabify_default_metaboxes', $defaults, $post_type );
		return apply_filters( 'tabify_default_metaboxes_' . $post_type , $defaults );
	}
}