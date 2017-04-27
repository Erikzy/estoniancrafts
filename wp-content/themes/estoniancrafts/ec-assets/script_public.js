
jQuery(document).ready(function($) {

    // Class toggle click
    $('.toggle-class').on('click', function(e)
    {
        e.preventDefault();

        var target = $(this).attr('data-target');
        var toggleClass = $(this).attr('data-toggle-class');

        $(target).toggleClass(toggleClass);
    });

});
