<!-- <link href="/module/top/style.css" rel="stylesheet"> -->

<?php
session_start();
if(isset($_SESSION['login']) && $_SESSION['login'] != 0){
    $login_display = 1;
}else{
    $login_display = 0;
}
?>

<div class="_title">
    <span>台灣大學生機系 — 系學會</span>
</div><!-- div class="_title" -->
<div class="top-toolbar">
    <div class="toolbar-left inherit-display">
        <div class="toolbar-display">
            <span class="ti-menu" id="top-menu-btn"></span>
        </div>
        <div class="hidden-1 ">
            <a href="/index.html"><span class="ti-home" id="top-home-btn"></span></a>
            <span class="ti-search" id="top-search-btn"></span>
        </div>
    </div><!-- div class="toolbar-left inherit-display" -->
    <div class="toolbar-right inherit-display">
        <div class="hidden-1 ">
            <a href="/profile/notify/index.php"><span class="ti-bell"></span></a>
        </div>
        <div class="toolbar-display">
            <span class="ti-user" id="top-profile-btn"></span>
        </div>
        <div class="hidden-1 ">
            <a <?php
                if($login_display){
                    echo('href="/profile/logout.php"');
                }else{
                    echo('href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal"');
                }
                ?>><span class="ti-power-off" id="top-power-btn"></span></a><!-- login btn link -->
            <style>
                #top-power-btn:hover {
                    <?php
                    if($login_display){
                        echo("color: red;");
                    }else{
                        echo("color: rgb(0, 162, 0);");
                    }
                    ?>
                    font-weight: bold;
                }
            </style>
        </div>
    </div><!-- div class="toolbar-right inherit-display" -->
</div><!-- div class="top-toolbar" -->
<div class="top-toolbar-detail detail-hidden">
    <div id="toolbar-menu" class="detail-list detail-flex detail-hidden">
        <a href="/index.html"><span>首頁</span></a>
        <a href="/about.html"><span>關於我們</span></a>
        <span>系產資源</span>
        <a href="/games/IamDog/index.html"><span>?????</span></a>
    </div>
    <div id="search-menu" class="detail-list detail-flex detail-hidden">
        <div id="search-form">
            <input id="search-text" type="text" placeholder="輸入您想要查詢的內容">
            <span class="ti-search" id="search-commit-btn"></span>
        </div>
        <script>
            $(document).ready(function(){
                $("#search-commit-btn").click(function(){
                    into_search();
                })
                $("#search-text").on('focus', function() {
                    top_list_freeze = 1;
                });
                $("#search-text").on('blur', function() {
                    top_list_freeze = 0;
                });
                $("#search-text").on('keydown', function(event) {
                    if (event.key === 'Enter') {
                        into_search()
                    }
                });
            })
            function into_search(){
                var search_text = $("#search-text").val();
                if(search_text == "微積分作業解答"){
                    search_text = "國立台灣大學學生重修相關規則";
                }
                search_text = encodeURIComponent(search_text);
                window.location.href = "https://www.google.com.tw/search?q="+search_text;
            }
        </script>
    </div><!-- div id="search-menu" -->
    <div id="profile-menu" class="detail-list detail-flex detail-hidden">
        <?php
            if($login_display){
                echo('
                    <a href="/profile/notify/index.php"><span>通知</span></a>
                    <a href="/profile/index.php"><span>個人設置</span></a>
                    <a href="/profile/logout.php"><span>登出</span></a>
                ');
            }else{
                echo('
                    <a href="https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal"><span>Line© 登入</span></a>
                    <a href="javascript:show_dev_login_form();"><span>Developer Login</span></a>
                ');
            }
        ?>
    </div>
</div><!-- div class="top-toolbar-menu1" -->

<!-- <script src="/module/top/top-menu-click.js"></script> -->
<!-- <script type="text/javascript" src="/module/js/dev-login.js"></script> -->
<!-- top/top-menu-click.js -->
<script>
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
</script>
<!-- /module/js/dev-login.js -->
<script>
function show_dev_login_form(){
    var url = "/module/dev-login-form.html"; // 替換成你想載入的 URL

    jQuery(".floating-content").load(url, function(response, status, xhr) {
        if (status == "error") {
            console.log("Error loading content: " + xhr.status + " " + xhr.statusText);
        }
        show_floatingWindow();
    });
}

jQuery(document).ready(function($){
    $("#uid-login-cancel").on("click", function(){
        close_floatingWindow();
    })
})
</script>
<!-- top/style.css -->
<style>
.page-top {
    background-color: #0000004a;
}

.page-top * {
    padding: 5px;
}

._title {
    display: flex;
    flex-direction: row;
    justify-content: center;
    background-color: #00000036;
}

._title span {
    font-size: 5vh;
}

.top-toolbar {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.top-toolbar span {
    padding: 0 10px !important;
    font-size: 3vh;
}

.top-toolbar-detail {
    min-height: 8vh;
    border-top: 3px inset;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.detail-flex {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
}

.detail-hidden {
    display: none;
}

#search-menu * {
    padding: 10px;
}

#search-menu input {
    width: 30vw;
    border: 3px inset;
    border-radius: 15px;
    background-color: #eaeaea;
    font-size: 18px;
}

#search-menu span {
    font-size: 25px;
}



.top-toolbar a {
    color: black;
}

.detail-list a {
    color: #000000;
}

.detail-list a:hover {
    color: #007f11;
}
</style>