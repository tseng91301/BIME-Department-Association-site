$(window).on('resize', function() {
    var width = $(window).width();
    var height = $(window).height();
    var rate = width/height;
    if(rate <= 1.33){
        $(".hidden-1").css({"display": "none"});
    }else{
        $(".hidden-1").css({"display": "unset"});
    }
});