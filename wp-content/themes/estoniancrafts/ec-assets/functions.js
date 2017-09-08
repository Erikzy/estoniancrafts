
jQuery(document).ready(function($) {
   $('.owl-carousel').owlCarousel({
    loop:true,
    margin:30,
	dots:true,
    responsiveClass:true,
    navText : ['<i class="fa fa-angle-left" aria-hidden="true"></i>','<i class="fa fa-angle-right" aria-hidden="true"></i>'],

    responsive:{
        0:{
            items:2,
            nav:true,
			margin:10
        },
        600:{
            items:2,
            nav:true,
			margin:10
        },
        1000:{
            items:3,
            nav:true,
            loop:false,
			margin:10
        }
    }
});	




    var $variation_forms = $('.variations_form');

    $variation_forms.each(function() {
        var $variation_form = $(this);

        if( $variation_form.data('swatches') ) return;
        $variation_form.data('swatches', true);

        $('.basel-swatch[selected="selected"]').addClass('active-swatch').attr('selected', false);

        $variation_form.on('click', '.swatches-select > div', function() {
            var value = $(this).data('value');
            var id = $(this).parent().data('id');

            $variation_form.trigger( 'check_variations', [ 'attribute_' + id, true ] );
            resetSwatches($variation_form);

            //$variation_form.find('select#' + id).val('').trigger('change');
            //$variation_form.trigger('check_variations');
            
            if ($(this).hasClass('active-swatch')) {
                $variation_form.find( '.variations select' ).val( '' ).change();
                $variation_form.trigger( 'reset_data' ); 
                return;
            }

            if ($(this).hasClass('swatch-disabled')) return;
            $variation_form.find('select#' + id).val(value).trigger('change');
            $(this).parent().find('.active-swatch').removeClass('active-swatch');
            $(this).addClass('active-swatch');
            resetSwatches($variation_form);
        })

        // Disable option fields that are unavaiable for current set of attributes

        .on('woocommerce_update_variation_values', function(event) {

        })

        // On clicking the reset variation button
        .on( 'click', '.reset_variations', function( event ) {
            $variation_form.find('.active-swatch').removeClass('active-swatch');
        } )

        .on('reset_data', function() {
            var all_attributes_chosen  = true;
            var some_attributes_chosen = false;

            $variation_form.find( '.variations select' ).each( function() {
                var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
                var value          = $( this ).val() || '';

                if ( value.length === 0 ) {
                    all_attributes_chosen = false;
                } else {
                    some_attributes_chosen = true;
                }

            });

            if( all_attributes_chosen ) {
                $(this).parent().find('.active-swatch').removeClass('active-swatch');
            }

            resetSwatches($variation_form);
        });
    })

    var resetSwatches = function($variation_form) {

        // If using AJAX 
        if( ! $variation_form.data('product_variations') ) return;

        $variation_form.find('select').each(function() {
            var select = $(this);
            var swatch = select.parent().find('.swatches-select');
            var options = $("select#" + this.id + " option").toArray();

            swatch.find('> div').removeClass('swatch-enabled').addClass('swatch-disabled');

            options.forEach(function(el) {
                var value = el.value;

                if($(el).hasClass('enabled') ) {
                    swatch.find('div[data-value="' + value + '"]').removeClass('swatch-disabled').addClass('swatch-enabled');
                } else {
                    swatch.find('div[data-value="' + value + '"]').addClass('swatch-disabled').removeClass('swatch-enabled');
                }

            });

        });
    };

});