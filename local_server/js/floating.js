function close_floatingWindow(){
    jQuery(".floating-content").html("");
    jQuery(".floating").css({
        "display": "none"
    });
}
function show_floatingWindow(){
    jQuery(".floating").css({
        "display": "block"
    });
}

jQuery(document).ready(function($){
    $(".floating-close").on("click", function(){
        close_floatingWindow();
    });
});