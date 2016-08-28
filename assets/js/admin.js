var UIX = UIX || {};

(function() {


    var ui = function(){

        var uixObjects      =   document.querySelectorAll('[data-uix]'),
            componentObjects=   document.querySelectorAll('[data-uix-component]'),
            uixComponents   =   {},
            activeObjects   =   {},
            activeTab       =   null,
            self            =   {},
            bindTabs        =   function( tabs ){
                for (var i = 0; i < tabs.length; i++) {
                    tabs[ i ].addEventListener('click', function (e) {
                        e.preventDefault();
                        var app = e.target.dataset.tab,
                            object  = self[ app ].object.dataset.uix;
                        //activeObjects
                        activeObjects[ object ].switchApp( app );
                    })
                }
            },
            activateTabs    =   function( tabs ){
                for (var i = 0; i < tabs.length; i++) {
                    tabs[ i ].classList.add('active');
                }
            },
            deactivateTabs  =   function( tabs ){
                for (var i = 0; i < tabs.length; i++) {
                    tabs[ i ].classList.remove('active');
                }
            },
            addComponent    = function( object, node, data ){
                var app         = object.app,
                    targets     = app.querySelectorAll('[data-component="' + node + '"]'),
                    template    = uixComponents[ node ];
                // register all UIX partials / components for this app
                for( var a = 0; a < targets.length; a++ ){
                    console.log( data );
                    var html = template( data );
                    //targets[a].innerHTML += html;
                    var wrapper= document.createElement('span');
                    wrapper.dataset.node = node;
                    wrapper.innerHTML= html;
                    targets[a].appendChild( wrapper );
                    console.log( wrapper );
                    //var div= wrapper.firstChild;
                }

            }

        // register all UIX partials / components
        for( var a = 0; a < componentObjects.length; a++ ){
            var component = componentObjects[a];
            //Handlebars.registerPartial( component.dataset.uixComponent, component.innerHTML );
            uixComponents[ component.dataset.uixComponent ] = Handlebars.compile( component.innerHTML, { data : true } );
        };

        for( var a = 0; a < uixObjects.length; a++ ){
            //self.
            var object  = uixObjects[a],
                apps    = object.querySelectorAll('[data-app]');

            // skip if there is no base UIX object
            if( !UIX[ object.dataset.uix ] ){ continue; }

            // add to activeObjects
            activeObjects[ object.dataset.uix ] = {
                apps        : [],
                switchApp   : function( app ){
                    for (var p = 0; p < this.apps.length; ++p) {
                        if( self[ this.apps[ p ] ].state ){
                            self[ this.apps[ p ] ].state = 0;
                        }
                    }
                    self[ app ].state = 1;
                }
            }

            // on all apps represented
            for( var t = 0; t < apps.length; t++ ){
                var app            = apps[t],
                    tabs           = object.querySelectorAll('[data-tab="' + app.dataset.app + '"]'),
                    template       = object.querySelectorAll('[data-template="' + app.dataset.app + '"]');

                if( !template.length ){ continue; }
                //register app with activeObjects
                activeObjects[ object.dataset.uix ].apps.push( app.dataset.app );

                self[ app.dataset.app ] = {
                    get state(){
                        return this._status;
                    },
                    set state(s){                       
                        if( s ){
                            this.app.style.display = 'block';
                            activateTabs( this.tabs );
                        }else{
                            this.app.style.display = 'none';
                            deactivateTabs( this.tabs );
                        }
                        this._status = s;
                    },
                    object  :   object,
                    app     :   app,
                    tabs    :   tabs,
                    get data(){
                        // first check if data structure has a parent.
                        if( UIX[ this.object.dataset.uix ].structure.parent &&  self[ UIX[ this.object.dataset.uix ].structure.parent ] ){
                            self[ UIX[ this.object.dataset.uix ].structure.parent ].state = 1;
                        }
                        return UIX[ this.object.dataset.uix ].data[ this.app.dataset.app ] || {};
                    },
                    set data( data ){
                        UIX[ this.object.dataset.uix ].data[ this.app.dataset.app ] = data;
                        this.render();
                    },
                    template    :   Handlebars.compile( template[0].innerHTML, { data : true } ),
                    render      :   function(){
                        var data = this.data;
                        if( typeof data === 'object' ){
                            this.app.innerHTML = this.template( data );                         
                        }
                        return this;
                    },
                    remove : function(){
                        this.app.innerHTML = null;
                        return this;
                    },
                    _status : 0
                };

                // bind tabs
                bindTabs( tabs );
                // init UI
                self[ app.dataset.app ].render();

                // initilize tabbing
                if( typeof UIX[ object.dataset.uix ].structure.tabs[ app.dataset.app ].default === "boolean" && UIX[ object.dataset.uix ].structure.tabs[ app.dataset.app ].default === true ){
                    self[ app.dataset.app ].state = 1;
                }

                // bind events for this app
                app.addEventListener('click', function (e) {
                    if( e.target.dataset.addNode ){
                        data = {};
                        if( e.target.dataset.nodeDefault ){
                            try{
                                var isjson = JSON.parse( e.target.dataset.nodeDefault );
                                data = isjson;
                            }catch(e){}
                        }
                        addComponent( self[ this.dataset.app ], e.target.dataset.addNode, data );
                    }
                });                
            }
        }

        return self;
    }

    // bind
    UIX.ui = new ui();

})( window );

        jQuery( function( $ ){
            $(document).on('click', '[data-save-object]', function(){
                for( var app in UIX.ui ){
                    console.log( UIX.ui[ app ].app );
                }
            });
        })