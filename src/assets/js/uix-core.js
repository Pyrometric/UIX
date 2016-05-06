function configObject() {
	var self = this
	if( uix.config !== null ){
		for( var key in uix.config ){
			self[key] = uix.config[key]
		}
	}
	riot.observable(self)
}
var config = new configObject()
function Conduit() {
	var self = this
	riot.observable(self)
	self.add_node = function(){
		console.log( arguments );
	}
	self.update = function(event){
		if( event.item ){
			event.item[ event.target.name ] = event.target.value
		}else{
			conduit.trigger( 'update', event, event.target.value )
		}
	}
}
var conduit = new Conduit()
riot.mount( 'uix-core' );
!( jQuery( function($){


	// init tabs
	$( document ).on('click', '[data-tab]', function(e){
		e.preventDefault();
		conduit.trigger('tab', $(this).data('tab') );
	});

	$( document ).on('click', '[data-save-object]', function(e){
		var spinner = $('.uix-save-spinner'),
			confirm = $('.uix-save-confirm');
		var data = {
			action : uix.slug + '_save_config',
			config : JSON.stringify( config ),
			page_slug : uix.page_slug,
			uix_setup : uix.nonce
		}
		spinner.css({display: 'inline-block'});
		$.post( ajaxurl, data, function( res ){
			spinner.hide();
			confirm.css({display: 'inline-block'}).fadeIn(function(){
				setTimeout( function(){
					confirm.fadeOut();
				}, 2000)
			})
		} );
	});
}) );