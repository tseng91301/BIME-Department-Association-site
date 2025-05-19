class Clickable {
    click_count = 0;
    contorl_element;
    constructor(element_name){
        this.contorl_element = $(element_name);
    }
    element(){
        return this.contorl_element;
    }
    click(){
        this.click_count += 1;
        if(this.click_count == 2){
            this.click_count = 0;
        }
        return this.click_count;
    }
    reset(){
        this.click_count = 0;
        return 0;
    }
}

var top_list_freeze = 0

$(document).ready(function(){
    $('#top-menu-btn').on('mouseenter', function() {
        show_list("#toolbar-menu");
    });
    var menu_btn = new Clickable('#top-menu-btn');
    $('#top-menu-btn').on('click', function() {
        if(menu_btn.click()){
            show_list("#toolbar-menu");
        }else{
            hide_list("#toolbar-menu");
        }
    });

    $('#top-search-btn').on('mouseenter', function() {
        show_list("#search-menu");
    });
    var search_btn = new Clickable('#top-search-btn');
    $('#top-search-btn').on('click', function() {
        if(search_btn.click()){
            show_list("#search-menu");
        }else{
            hide_list("#search-menu");
        }
    });

    $('#top-profile-btn').on('mouseenter', function() {
        show_list("#profile-menu");
    });
    var search_btn = new Clickable('#top-profile-btn');
    $('#top-profile-btn').on('click', function() {
        if(search_btn.click()){
            show_list("#profile-menu");
        }else{
            hide_list("#profile-menu");
        }
    });


    $('.top-toolbar-detail').on('mouseleave', function() {
        hide_all_list();
    });

    
});
    
function show_list(id){
    $('.detail-list').addClass('detail-hidden');
    $(id).removeClass('detail-hidden');
    $('.top-toolbar-detail').removeClass('detail-hidden');
}

function hide_list(id){
    $(id).addClass('detail-hidden');
    $('.top-toolbar-detail').addClass('detail-hidden');
}

function hide_all_list(){
    if(top_list_freeze){
        return;
    }
    $('.detail-list').addClass('detail-hidden');
    $('.top-toolbar-detail').addClass('detail-hidden');
}