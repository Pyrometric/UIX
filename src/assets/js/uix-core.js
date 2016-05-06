function Conduit(opts) {
	var self = this
	self.config = opts
	riot.observable(self)
}
if( uix.config === null ){
	uix.config = {}
}

riot.mixin( { uix : uix } )
var conduit = new Conduit( uix.config )

!( jQuery( function($){

	var currentTab = null,
		instance;
	
	instance = riot.mount( 'core' );

	$( document ).on('input change', 'input,select,checkbox,radio', function(e){
		conduit.trigger( 'update', e, e.target.value )
	});

	// init tabs
	$( document ).on('click', '[data-tab]', function(e){
		e.preventDefault();
		var clicked = $(this),
			tab = clicked.data('tab');
		
		if( currentTab !== null ){			
			//instanceTags[ currentTab ][0].unmount( true );
		}
		//instanceTags[ tab ] = riot.mount( '.uix-tab-canvas', tab );
		currentTab = tab;
		$('[data-tab].active').removeClass('active');
		clicked.addClass('active');
		conduit.trigger('tab', tab);
	});

	$( document ).on('click', '[data-save-object]', function(e){
		var data = {
			action : uix.slug + '_save_config',
			config : JSON.stringify( conduit.config ),
			page_slug : uix.page_slug,
			uix_setup : uix.nonce
		}
		$('.uix-save-spinner').css({display: 'inline-block'});
		$.post( ajaxurl, data, function( res ){
			$('.uix-save-spinner').hide();			
		} );
	});

	// whao!
	//console.log( uix );

}) );