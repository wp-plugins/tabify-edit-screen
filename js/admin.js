jQuery(function($) {
	// Remove nojs support
	$( ".tabify_control .item-order" ).remove();
	$( "#tabify_edit_screen_nojs" ).remove();

	$( ".tabify_control" ).sortable({
		scroll : false
	});

	// Initialize sortables
	initialize_sortable_ul();
	function initialize_sortable_ul() {
		$( ".tabify_control ul" ).sortable({
			//items : ".steps",
			connectWith: ".tabify_control ul",
			scroll : false,
			disableSelection: true,
			receive: function(event, ui) {
				var item = $( ui.item );
				var parts = $( 'input', ui.item ).attr('name').split( '][' );
				parts[2] = item.closest( 'div' ).index();
				$( 'input', ui.item ).attr( 'name', parts.join( '][' ) );
			}
		});
	}

	$( "#create_tab" ).on("click", function() {
		var title = 'Choose title';
		var posttype = $( '.nav-tab-active' ).attr( 'id' );
		posttype = posttype.replace( 'tab-', "");

		var counter = $( '.tabifybox-' + posttype + ' .tabify_control' ).children().length;

		var html = '<div>';
		html += '<h2><span>' + title + '</span><input type="text" name="tabify[' + posttype + '][tabs][' + counter + '][title]" value="' + title + '" style="display: none;" /></h2>';
		html += '<ul></ul></div>';

		$( '.tabifybox-' + posttype + ' .tabify_control' ).append( html );

		$( '.tabifybox-' + posttype + ' .tabify_control' ).sortable( "refresh" );
		initialize_sortable_ul();
	});

	// Make the h2 changeable by a click
	$( document ).on("click", ".tabifybox h2", function(){
		$( 'span', this ).hide();
		$( 'input', this ).show();
		$( 'input', this ).focus();
	});

	$( document ).on("focusout", ".tabifybox h2 input", function(){
		$( this ).hide();
		$( this ).closest( 'h2' ).find('span').html( $( this ).val() );
		$( this ).closest( 'h2' ).find('span').show();
	});
});