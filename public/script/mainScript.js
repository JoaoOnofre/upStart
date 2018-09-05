$(window).scroll(function() {
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
        $('footer').slideDown(300);
    } else {
        $('footer').slideUp(300);
    }
});


$(document).ready(function() {


    $('#toggler').click(function(e) {
        e.preventDefault();
        $('#left-navbar').slideToggle(500);
        $(this).toggleClass('navbar-show');

    });
});