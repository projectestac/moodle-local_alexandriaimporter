$(function() {
    $(".details_toggle").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggle_advanced(!$(".advanced").is(":visible"));
    });
});