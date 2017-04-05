var baselAdminModule, basel_media_init;

(function($) {
    "use strict";

    baselAdminModule = (function() {

        var baselAdmin = {

            interval: 0,

            form: $('#basel-import-form'),

            responseArea: $('#basel-import-form').find('.basel-response'),

            progressBar: $('#basel-import-form').find('.basel-import-progress'),

            verSelect: $('#basel-import-form').find('#basel_version'),

            importAction: function() {

                baselAdmin.form.submit(function(e){
                    e.preventDefault();

                    if( baselAdmin.form.hasClass('form-in-action') ) return;

                    baselAdmin.form.addClass('form-in-action');

                    clearInterval( baselAdmin.intervalClerer );

                    baselAdmin.runInititalLoadings( 30, 50, 70 );

                    var data = $(this).serialize(),
                        selected = baselAdmin.verSelect.find(":selected");

                    data += "&action=basel_import_data";

                    console.log(data);

                    baselAdmin.clearResponseArea();

                    baselAdmin.callImportAJAX( data );

                });
            },

            callImportAJAX: function( data ) {
                $.ajax({
                    url: baselConfig.ajax,
                    data: data,
                    timeout: 1000000,
                    success: function( response ) {

                        var rJSON = { status: '', message: '' };

                        try {
                            rJSON = JSON.parse(response);
                        } catch( e ) {}           

                        if( ! response ) {
                            baselAdmin.responseArea.html( '<div class="basel-warning">Empty AJAX response, please try again.</div>' ).fadeIn();
                        } else if( rJSON.status == 'success' ) {
                            baselAdmin.responseArea.html( '<div class="basel-success">' + rJSON.message + '</div>' ).fadeIn();
                        } else if( rJSON.status == 'fail' ) {
                            baselAdmin.responseArea.html( '<div class="basel-error">' + rJSON.message + '</div>' ).fadeIn();
                        } else {
                            baselAdmin.responseArea.html( '<div class="">' + response + '</div>' ).fadeIn();
                        }

                    },
                    error: function( response ) {
                        baselAdmin.responseArea.html( '<div class="basel-warning">Import AJAX problem. Please, try import data manually.</div>' ).fadeIn();
                        console.log('import ajax ERROR');
                    },
                    complete: function() {

                        baselAdmin.clearInitialLoadings();

                        baselAdmin.form.removeClass('form-in-action');

                        baselAdmin.updateProgress( baselAdmin.progressBar, 100, 0 );

                        baselAdmin.progressBar.parent().find('.basel-notice').remove();

                        baselAdmin.intervalClerer = setTimeout(function() {
                            baselAdmin.destroyProgressBar(200);
                        }, 2000 );

                        //console.log('import ajax complete');
                    },
                });
            },

            runInititalLoadings: function(fake1progress, fake2progress, noticeProgress) {

                baselAdmin.destroyProgressBar(0);

                baselAdmin.updateProgress( baselAdmin.progressBar, fake1progress, 350 );

                this.fake2timeout = setTimeout( function() {
                    baselAdmin.updateProgress( baselAdmin.progressBar, fake2progress, 100 );
                }, 25000 );

                this.noticeTimeout = setTimeout( function() {
                    baselAdmin.updateProgress( baselAdmin.progressBar, noticeProgress, 100 );
                    baselAdmin.progressBar.after( '<p class="basel-notice small">Please, wait. Theme needs much time to download all attachments</p>' );
                }, 60000 );

                this.errorTimeout = setTimeout( function() {
                    baselAdmin.progressBar.parent().find('.basel-notice').remove();
                    baselAdmin.progressBar.after( '<p class="basel-notice small">Something wrong with import. Please, try to import data manually</p>' );
                }, 3100000 );
            },

            clearInitialLoadings: function() {
                clearTimeout( this.fake2timeout );
                clearTimeout( this.noticeTimeout );                          
                clearTimeout( this.errorTimeout );
            },

            destroyProgressBar: function( hide ) {
                baselAdmin.progressBar.hide( hide ).attr('data-progress', 0).find('div').width(0);
            },

            clearResponseArea: function() {
                this.responseArea.fadeOut(200, function() {
                    $(this).html( '' );
                });
            },

            updateProgress: function( el, to, interval ) {
                el.show();

                clearInterval( baselAdmin.interval );

                var from = el.attr('data-progress'),
                    delta = to - from,
                    i = from;

                if( interval == 0 ) {
                    el.attr('data-progress', 100).find('div').width(el.attr('data-progress') + '%');
                } else {
                    baselAdmin.interval = setInterval(function() {
                        i++;
                        el.attr('data-progress', i).find('div').width(el.attr('data-progress') + '%');
                        if( i == to ) clearInterval( baselAdmin.interval );
                    }, interval);
                }

            },
            pagesPreviews: function() {
                var preview = baselAdmin.form.find('.page-preview'),
                    image = preview.find('img'),
                    dir = image.data('dir'),
                    newImage = '';

                image.on('load', function() {
                  // do stuff on success
                    $(this).removeClass('loading-image');
                }).on('error', function() {
                  // do stuff on smth wrong (error 404, etc.)
                    $(this).removeClass('loading-image');
                }).each(function() {
                    if(this.complete) {
                      $(this).load();
                    } else if(this.error) {
                      $(this).error();
                    }
                });

                baselAdmin.verSelect.on('change', function() {
                    var page = $(this).val();

                    if( page == '' || page == '--select--' ) page = 'base';

                    newImage = dir + '/' + page + '/preview.jpg';

                    image.addClass('loading-image').attr('src', newImage);
                });
            },

            attributesMetaboxes: function() {

                if( ! $('body').hasClass('product_page_product_attributes') ) return;

                var orderByRow = $('#attribute_orderby').parent(),
                    orderByTableRow = $('#attribute_orderby').parents('tr'),
                    selectedSize = ( baselConfig.attributeSwatchSize != undefined && baselConfig.attributeSwatchSize.length > 1 ) ? baselConfig.attributeSwatchSize : '',
                    label = '<label for="attribute_swatch_size">Attributes swatch size</label>',
                    description = '<p class="description">If you will set color or images swatches for terms of this attribute.</p>',
                    select = [
                        '<select name="attribute_swatch_size" id="attribute_swatch_size">',
                            '<option value="default"' + (( selectedSize == 'default' ) ?  ' selected="selected"' : '') + '>Default</option>',
                            '<option value="large"' + (( selectedSize == 'large' ) ?  ' selected="selected"' : '') + '>Large</option>',
                            '<option value="xlarge"' + (( selectedSize == 'xlarge' ) ?  ' selected="selected"' : '') + '>Extra large</option>',
                        '</select>',
                    ].join(''),
                    metaHTMLTable = [
                        '<tr class="form-field form-required">',
                            '<th scope="row" valign="top">',
                                label,
                            '</th>',
                            '<td>',
                                select,
                                description,
                            '</td>',
                        '</tr>'
                    ].join(''),
                    metaHTMLParagraph = [
                        '<div class="form-field">',
                            label,
                            select,
                            description,
                        '</div>'
                    ].join('');

                console.log(orderByTableRow.length);

                if( orderByTableRow.length > 0 ) {
                    orderByTableRow.after( metaHTMLTable );
                } else {
                    orderByRow.after( metaHTMLParagraph );
                }
            },

            product360ViewGallery: function() {

                // Product gallery file uploads.
                var product_gallery_frame;
                var $image_gallery_ids = $( '#product_360_image_gallery' );
                var $product_images    = $( '#product_360_images_container' ).find( 'ul.product_360_images' );

                $( '.add_product_360_images' ).on( 'click', 'a', function( event ) {
                    var $el = $( this );

                    event.preventDefault();

                    // If the media frame already exists, reopen it.
                    if ( product_gallery_frame ) {
                        product_gallery_frame.open();
                        return;
                    }

                    // Create the media frame.
                    product_gallery_frame = wp.media.frames.product_gallery = wp.media({
                        // Set the title of the modal.
                        title: $el.data( 'choose' ),
                        button: {
                            text: $el.data( 'update' )
                        },
                        states: [
                            new wp.media.controller.Library({
                                title: $el.data( 'choose' ),
                                filterable: 'all',
                                multiple: true
                            })
                        ]
                    });

                    // When an image is selected, run a callback.
                    product_gallery_frame.on( 'select', function() {
                        var selection = product_gallery_frame.state().get( 'selection' );
                        var attachment_ids = $image_gallery_ids.val();

                        selection.map( function( attachment ) {
                            attachment = attachment.toJSON();

                            if ( attachment.id ) {
                                attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
                                var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

                                $product_images.append( '<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>' );
                            }
                        });

                        $image_gallery_ids.val( attachment_ids );
                    });

                    // Finally, open the modal.
                    product_gallery_frame.open();
                });

                // Image ordering.
                $product_images.sortable({
                    items: 'li.image',
                    cursor: 'move',
                    scrollSensitivity: 40,
                    forcePlaceholderSize: true,
                    forceHelperSize: false,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'wc-metabox-sortable-placeholder',
                    start: function( event, ui ) {
                        ui.item.css( 'background-color', '#f6f6f6' );
                    },
                    stop: function( event, ui ) {
                        ui.item.removeAttr( 'style' );
                    },
                    update: function() {
                        var attachment_ids = '';

                        $( '#product_360_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                            var attachment_id = $( this ).attr( 'data-attachment_id' );
                            attachment_ids = attachment_ids + attachment_id + ',';
                        });

                        $image_gallery_ids.val( attachment_ids );
                    }
                });

                // Remove images.
                $( '#product_360_images_container' ).on( 'click', 'a.delete', function() {
                    $( this ).closest( 'li.image' ).remove();

                    var attachment_ids = '';

                    $( '#product_360_images_container' ).find( 'ul li.image' ).css( 'cursor', 'default' ).each( function() {
                        var attachment_id = $( this ).attr( 'data-attachment_id' );
                        attachment_ids = attachment_ids + attachment_id + ',';
                    });

                    $image_gallery_ids.val( attachment_ids );

                    // Remove any lingering tooltips.
                    $( '#tiptip_holder' ).removeAttr( 'style' );
                    $( '#tiptip_arrow' ).removeAttr( 'style' );

                    return false;
                });
            }
        };

        return {
            init: function() {

                baselAdmin.importAction();
                baselAdmin.pagesPreviews();

                $(document).ready(function() {
                    baselAdmin.attributesMetaboxes();
                    baselAdmin.product360ViewGallery();
                });

            },

            mediaInit: function(selector, button_selector, image_selector)  {
                var clicked_button = false;
                $(selector).each(function (i, input) {
                    var button = $(input).next(button_selector);
                    button.click(function (event) {
                        event.preventDefault();
                        var selected_img;
                        clicked_button = $(this);
             
                        // check for media manager instance
                        if(wp.media.frames.gk_frame) {
                            wp.media.frames.gk_frame.open();
                            return;
                        }
                        // configuration of the media manager new instance
                        wp.media.frames.gk_frame = wp.media({
                            title: 'Select image',
                            multiple: false,
                            library: {
                                type: 'image'
                            },
                            button: {
                                text: 'Use selected image'
                            }
                        });
             
                        // Function used for the image selection and media manager closing
                        var gk_media_set_image = function() {
                            var selection = wp.media.frames.gk_frame.state().get('selection');
             
                            // no selection
                            if (!selection) {
                                return;
                            }
             
                            // iterate through selected elements
                            selection.each(function(attachment) {
                                var url = attachment.attributes.url;
                                clicked_button.prev(selector).val(attachment.attributes.id);
                                $(image_selector).attr('src', url).show();
                            });
                        };
             
                        // closing event for media manger
                        wp.media.frames.gk_frame.on('close', gk_media_set_image);
                        // image selection event
                        wp.media.frames.gk_frame.on('select', gk_media_set_image);
                        // showing media manager
                        wp.media.frames.gk_frame.open();
                    });
               });
            }

        }

    }());

})(jQuery);

basel_media_init = baselAdminModule.mediaInit;

jQuery(document).ready(function() {
    baselAdminModule.init();
});
