jQuery(function($) {
	$( ".tabify-tab" ).on("click", function( evt ) {
		evt.preventDefault();
		$( ".tabify-tab" ).removeClass( 'nav-tab-active' );
		$( this ).addClass( 'nav-tab-active' );

		var id = evt.target.id.replace( 'tab-', "");
		$( ".tabifybox" ).hide();
		$( ".tabifybox-" + id ).show();
		$( "#current_tab" ).val( id );
	});
});