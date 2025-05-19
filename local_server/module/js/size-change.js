$(window).on('resize', function() {
    reset_frame();
});
$(document).ready(function(){
    reset_frame(1);
});
function reset_frame(first_time = 0){
    var width = $(window).width();
    var height = $(window).height();
    console.log(width);
    console.log(height);
    var rate = width/height;
    if(rate <= 1.33){
        $(".hidden-1").css({"display": "none"});
    }else{
        $(".hidden-1").css({"display": "unset"});
    }
    if(first_time){
        setTimeout(reset_frame, 5000);
    }
}