(function() {

    jQuery( function( $ ){

        $( document ).on( 'click', '.uix-repeatable', function( e ){
            var clicked     = $( this ),
                slug        = clicked.data('slug'),
                template    = $( '#tmpl-' + clicked.data('for') ).html();

                var res = $( template.replace(/{__slug__}/g, slug ) );
            res.insertBefore( clicked );
        });

    });

})( jQuery );