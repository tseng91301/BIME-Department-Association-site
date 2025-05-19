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