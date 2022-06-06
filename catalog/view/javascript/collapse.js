$(document).ready(function() {

    $('.collapse').on('show.bs.collapse', function() {
        var collapse = this;
        var figure = $(collapse).prev().find('.fa');

        $(figure).toggleClass('fa-plus fa-minus');
    });

    $('.collapse').on('hide.bs.collapse', function() {
        var collapse = this;
        var figure = $(collapse).prev().find('.fa');

        $(figure).toggleClass('fa-minus fa-plus');
    });


});
