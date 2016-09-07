(function() {
    jQuery( function( $ ){
        $( document ).on( 'click', '.uix-tab-trigger', function( e ){

            var clicked     = $( this ),
                id          = this.hash.substr(1),
                target      = $('[id^="' + id + '_"]'),
                parent      = $( '#' + clicked.data( 'parent' ) ),
                sections    = parent.find( '.uix-sections > [aria-hidden="false"]' );

            sections.attr( 'aria-hidden', true );
            target.attr( 'aria-hidden', false );
            console.log( target );

        } );
    });

})( jQuery );