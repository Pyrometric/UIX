!(function ($) {

    $.fn.uixLayout = function (opts) {

        return this.each(function () {

            var grid = $(this);
            var grid_object;
            var current_input = $('#' + grid.prop('id') + '-control');
            var current_modal;
            var active_column;
            var grid_active = false;
            var sort_column_handle;
            var sort_column;
            var sort_column_container;


            grid.addClass('_uix_grid_init');

            // setup loading
            grid.html('<div class="uix-loading" style=""></div>');

            var init_builder = function () {
                // is current template parsable , or not
                var input_value = current_input.val();

                if (input_value.length) {

                    try {
                        var serialization = JSON.parse(input_value);
                        if (typeof serialization === 'object') {
                            if (serialization.rows && serialization.rows.length) {
                                for (var r = 0; r < serialization.rows.length; r++) {
                                    var row = add_row(),
                                        row_struct = serialization.rows[r];
                                    if (row_struct.length) {
                                        for (var c = 0; c < row_struct.length; c++) {
                                            var column_struct = row_struct[c],
                                                column = add_column(row, column_struct.span),
                                                container = column.find('.column-container');
                                            if (column_struct.contents && column_struct.contents.length) {
                                                for (var co = 0; co < column_struct.contents.length; co++) {
                                                    var content = column_struct.contents[co],
                                                        component = add_component(content.type, container, content.config );

                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        // init rows
                        init_rows();
                    }
                    catch (err) {
                        console.log(err);
                    }
                }
                // remove loader
                grid.find('.uix-loading').remove();
            }

            var create_component = function (type, data) {

                var component = $('<div class="uix-component component-' + type + '" data-type="' + type + '" data-label="' + type + '"><div class="uix-component-toolbar"><span class="dashicons dashicons-admin-generic uix-component-edit"></span> ' + type + '<span class="dashicons dashicons-no remove-component right"></span></div></div>'),
                    template = Handlebars.compile( $( '#' + grid.prop('id') + '-' + type ).html() );

                // load component template
                component.append( template( data ) );
                component.data('config', data );

                return component;
            }

            var add_component = function (type, container, data) {

                var component = create_component( type, data );

                component.appendTo(container);

                save_current_edit();
                return component;
            }
            var remove_component = function (component) {
                component.fadeOut(200, function () {
                    component.remove();
                    save_current_edit();
                });
            }
            var set_component = function (component) {

            }
            var add_row = function () {
                var row = $('<div class="row"><span class="remove-row"><span class="dashicons dashicons-no"></span></span></div>');
                row.appendTo(grid);
                save_current_edit();
                return row;
            }
            var remove_row = function (row) {
                destroy_grid();
                row.fadeOut(200, function () {
                    row.remove();
                    save_current_edit();
                });
                init_rows();
            }

            var add_column = function (row, column_size, index) {
                var column = $('<div class="col-xs-' + column_size + ' column"><div class="column-container"><span class="splitter"><span class="dashicons dashicons-leftright"></span></span><span class="column-remover"><span class="dashicons dashicons-minus"></span></span><span class="add-component"><span class="dashicons dashicons-plus-alt"></span></span></div></div>'),
                    columns = row.find(' > .column');

                if (typeof index !== 'undefined') {
                    column.insertAfter(columns.eq(index));
                } else {
                    column.appendTo(row);
                }

                save_current_edit();
                return column;
            }
            var remove_column = function (column) {

                destroy_grid();

                var contents = column.find('.uix-component'),
                    row = column.closest('.row'),
                    reciever = column.next('.column');

                if (!reciever.length) {
                    reciever = column.prev('.column');
                }

                if (reciever.length) {
                    // move stuffs and resize
                    var reciever_size = get_column_size(reciever);
                    var column_size = get_column_size(column);
                    reciever.removeClass('col-xs-' + reciever_size).addClass('col-xs-' + ( reciever_size + column_size ));
                    if (contents.length) {
                        reciever.find('.column-container').append(contents);
                    }
                }

                column.remove();
                if (!reciever.length) {
                    remove_row(row);
                } else {
                    init_rows();
                }
                save_current_edit();
            }



            // save current edit
            var save_current_edit = function () {
                var config = {
                        rows: []
                    },
                    item;


                var rows = grid.find(' > .row');
                for (var r = 0; r < rows.length; r++) {
                    var row = $(rows[r]),
                        columns = row.find('> .column'),
                        row_conf = [];

                    for (var c = 0; c < columns.length; c++) {

                        // get content
                        var column = $(columns[c]),
                            component_struct = [],
                            components = column.find('> .column-container > .uix-component');

                        if (components.length) {
                            for (var co = 0; co < components.length; co++) {
                                var component = $(components[co]);
                                component_struct.push({
                                    type: component.data('type'),
                                    config : component.data('config'),
                                });
                            }
                        }
                        row_conf.push({
                            span: get_column_size(column),
                            contents: component_struct
                        });

                    }

                    config.rows.push(row_conf);


                }
                grid_object = config;
                current_input.val(JSON.stringify(grid_object));
            }

            //  add element
            grid.on('click', '.uix-grid-element', function () {
                var clicked = $(this),
                    type = clicked.data('type'),
                    label = clicked.data('label'),
                    field = clicked.data('config_field') ? clicked.data('config_field') : '';

                // add to grid

            });


            $(document).on('click', '[for="' + grid.prop('id') + '-control"]', function () {
                console.log(grid);
                destroy_grid();
                var row = add_row();
                add_column(row, 12);
                init_rows();
            });

            grid.on('click', '.remove-component', function () {
                var component = $(this).closest('.uix-component');
                if (confirm('Really?')) {
                    remove_component(component);
                }

            });


            grid.on('click', '.remove-row', function () {
                var row = $(this).closest('.row'),
                    contents = row.find('.uix-component');
                if (contents.length) {
                    if (false === confirm('Really?')) {
                        return;
                    }
                }
                remove_row(row);

            });


            // trigger component modal
            var component_editor = function () {
                var clicked = $( this ),
                    template_ele = $( '#' + clicked.data('id') + '-tmpl' ),
                    template = Handlebars.compile( template_ele.html() ),
                    component_modal = $.uixModal({
                        modal: clicked.data('id'),
                        title: clicked.data('label'),
                        width : template_ele.data('width'),
                        height: template_ele.data('height'),
                        footer: ' ',
                        focus: true,
                    });


                component_modal.content.html( template( {} ) );
                if( ! clicked.data('setup') ){
                    component_modal.modal.addClass('processing');
                    component_modal.footer.hide();
                    component_modal.modal.submit();
                }else{
                    component_modal.footer.html( $( '#' + clicked.data('id') + '-footer-tmpl' ).html() );
                }
                component_modal.modal.on('modal.complete', function(){
                    add_component( clicked.data('type') , active_column, component_modal.data );
                    current_modal.closeModal();
                });
                $(document).trigger('uix.init');
            }
            grid.on('click', '.uix-component-edit', function () {
                var clicked = $(this),
                    component = clicked.closest('.uix-component'),
                    template_ele = $( '#' + grid.prop('id') + '-' + component.data('type') + '-tmpl' ),
                    template = Handlebars.compile( template_ele.html() );

                var component_modal = $.uixModal({
                    modal: grid.prop('id') + '-' + component.data('type'),
                    title: component.data('label'),
                    footer: '#' + grid.prop('id') + '-' + component.data('type') + '-footer-tmpl',
                    width : template_ele.data('width'),
                    height: template_ele.data('height'),
                    focus: true,
                });

                component_modal.content.html( template( component.data('config') ) );
                component_modal.positionModals();
                component_modal.modal.on('modal.complete', function(){
                    var updated = create_component( component.data('type') , component_modal.data );
                    component.replaceWith( updated );
                    component_modal.closeModal();
                });
                $(document).trigger('uix.init');
            });

            grid.on('click', '.add-component', function () {
                var clicked = $(this),
                    template_ele = $('#' + grid.prop('id') + '-components'),
                    template = Handlebars.compile( template_ele.html() );

                active_column = clicked.parent();

                current_modal = $.uixModal({
                    modal: 'uix_component',
                    title: 'Insert',
                    width : 550,
                    height: 445,
                    focus: true,
                });
                current_modal.content.html( template( {} ) );
                current_modal.title.css('background', grid.data('color') );
                $( current_modal.content ).on('click', '.uix-component-trigger', component_editor );
                current_modal.positionModals();
            });

            grid.on('click', '.column-remover', function () {
                var column = $(this).closest('.column');
                remove_column(column);
            });

            grid.on('click', '.splitter', function () {
                var row = $(this).closest('.row'),
                    column = $(this).closest('.column');

                var columns = row.find(' > .column'),
                    column_size = 12,
                    last_column = null;

                if (columns.length) {

                    if (columns.length >= 12) {
                        return;
                    }
                    var last_size = get_column_size(column);
                    column.removeClass('col-xs-' + last_size).addClass('col-xs-' + (Math.ceil(last_size / 2)));
                    column_size = 1;
                }
                destroy_grid();

                add_column(row, Math.floor(last_size / 2), columns.index(column));

                init_rows();
            });

            // reload testing - remove later

            function get_column_size(column) {
                var last_classes = column.prop('class').split(' ');
                for (var i = 0; i < last_classes.length; i++) {
                    if (last_classes[i].substr(0, 6) === 'col-xs') {
                        return parseFloat(last_classes[i].split('-')[2]);
                    }
                }
            }

            function destroy_grid() {

                if (grid_active) {
                    //grid.find('.column-handle').draggable("destroy").remove();
                    //grid.find('.column').droppable("destroy");
                    //grid.find('.column-container').sortable("destroy");
                    //grid.sortable("destroy");
                    grid_active = false;
                }
            }

            function init_rows() {

                grid.sortable({
                    //handle : '.uix-row-toolbar',
                    //axis: "y",
                    connectWith: ".uix-control-layout",
                    delay: 150,
                    forceHelperSize: true,
                    //cursorAt: {top: 35, left: 74},
                    forcePlaceholderSize: true,
                    update: function () {
                        save_current_edit();
                    },
                    helper: function (e, item) {
                        return $('<div class="uix-row-helper" style="width: 155px; height: 30px;"></div>');
                    },
                });

                sort_column_container = grid.find(".column-container").sortable({
                    //handle : '.uix-component-toolbar',
                    delay: 150,
                    forceHelperSize: false,
                    items: '.uix-component',
                    appendTo: "body",
                    connectWith: ".column-container",
                    cursorAt: {top: 35, left: 74},
                    zIndex: 1000000,
                    helper: function (e, item) {
                        return $('<div class="uix-component-helper" style="width: 155px; height: 30px;" data-type="' + item.data('type') + '" data-label="' + item.data('label') + '">' + item.data('label') + '</div>');
                    },
                    update: function () {
                        //jQuery('#support-id').trigger('change');
                    }
                });
                grid.find('.row').each(function () {
                    var row = $(this),
                        spans = (row.width() / 12).toFixed(3),
                        left_start = null,
                        right_start = null,
                        left_last = null,
                        right_last = null,
                        left = null,
                        right = null;

                    row.find('> .column').append('<span class="column-handle"></span>');
                    row.find('> .column > .column-handle').last().remove();

                    sort_column_handle = row.find('> .column > .column-handle').draggable({
                        axis: "x",
                        containment: '.row',
                        helper: 'clone',
                        grid: [spans],
                        start: function (e, u) {
                            row.addClass('row-active');
                            left = u.helper.parent(),
                                right = left.next();

                            // left
                            left_start = get_column_size(left);
                            right_start = get_column_size(right);

                        },
                        drag: function (e, u) {

                            var distance = ( u.position.left - u.originalPosition.left ).toFixed(3);
                            if (isNaN(distance) || ( right_start - ( distance / spans ) < 1 ) || ( left_start + ( distance / spans ) < 1 )) {

                                return;
                            }
                            // left
                            left.removeClass('col-xs-' + left_start).removeClass(left_last);
                            left_last = 'col-xs-' + ( left_start + ( distance / spans ) ).toFixed();
                            left.addClass(left_last);
                            // right
                            right.removeClass('col-xs-' + right_start).removeClass(right_last);
                            right_last = 'col-xs-' + ( right_start - ( distance / spans ) ).toFixed();
                            right.addClass(right_last);

                        },
                        stop: function () {
                            row.removeClass('row-active');
                            save_current_edit();
                        }

                    });
                    // dropable
                    sort_column = row.find(".column").droppable({
                        accept: '.uix-uixbuilder-layout-layout_grid-first_row-0-settings-conditions-mini',
                        greedy: true,
                        hoverClass: "drop-hover",
                        drop: function (event, ui) {
                            var container = $(this).find('.column-container');
                            add_component(ui.helper.data('type'), container);
                            $('.drop-hover').removeClass('drop-hover');
                        }
                    });
                });


                // set active
                grid_active = true;
            }

            /// components
            $(".uix-uixbuilder-layout-layout_grid-first_row-0-settings-conditions-mini").draggable({
                cursor: "move",
                appendTo: "body",
                cursorAt: {top: 15},
                helper: function (event) {
                    var item = $(event.target);
                    return $('<div class="uix-component-helper" data-type="' + item.data('type') + '" data-label="' + item.data('label') + '">' + item.html() + '</div>');
                }
            });

            // init
            init_builder();
            $(document).trigger('grid.init');
        });
    };


    // init
    $(window).load(function () {
        $(document).on('uix.init', function () {
            $('.uix-control-layout').not('._uix_grid_init').uixLayout();
        });
    });

})(jQuery);
