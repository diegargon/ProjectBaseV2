/**
 *  News - scroll js file
 *
 *  @author diego////@////envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2021 Diego Garcia (diego////@////envigo.net) 
 */

$(window).scroll(function () {
    if ($(this).scrollTop() >= $(window).height()) {
        $('#scrollup').fadeIn(300);
    } else {
        $('#scrollup').fadeOut(300);
    }
    if ($(this).scrollTop() >= $(document).height() - ($(window).height() * 2)) {
        $('#scrolldown').fadeOut(300);
    } else {
        $('#scrolldown').fadeIn(300);
    }
});
$('#scrollup').click(function () {      // When arrow is clicked
    $('body,html').animate({
        scrollTop: 0                       // Scroll to top of body
    }, 500);
});

$('#scrolldown').click(function () {      // When arrow is clicked
    $("html, body").animate({scrollTop: $(document).height() - $(window).height()});
});
