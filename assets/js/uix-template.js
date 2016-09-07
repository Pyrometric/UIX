(function() {

    jQuery( function( $ ){

        $( document ).on( 'click', '.uix-repeatable', function( e ){
            var clicked     = $( this ),
                slug        = clicked.data('slug'),
                id          = clicked.data('for'),
                template    = $( '#tmpl-' + id ).html(),
                counter     = $('[id^="' + id + '_"]').length,
                regex_id    = new RegExp( id + '_0', "g" ),
                regex_count = new RegExp( '[0]', "g" ),
                template    = template.replace( regex_id, id + '_' + counter ),
                res         = template.replace( regex_count, counter );
            
            $( res ).insertBefore( clicked ).attr('aria-hidden', false);
        });

    });

})( jQuery );