RESOURCE_VERSION = "1.0.0"

const local_resources = {
    "/css/themify-icons.css": "themify-icons",
    "/module/css/global-style.css": "global-style",
    "/module/foot/style.css": "foot-style",
    "/module/js/size-change.js": "size-change-js",
    "/module/js/floating.js": "floating-js",
    "/module/js/dev-login.js": "dev-login-js",
    "/module/top/dev-login.js": "top-dev-login-js",
    "/module/top/style.css": "top-style",
    "/module/top/top-menu-click.js": "top-menu-click-js",
    "/module/dev-login-form.html": "dev-login-form-html",
    "/module/foot.html": "page-footer",
}

function loadScript(url) {
    const script = document.createElement('script');
    script.src = url;
    script.type = 'text/javascript';
    script.async = true; // 非同步加載
    document.head.appendChild(script);
}

function load_local_data(name){
    const data = localStorage.getItem(name);
    return data;
}

function store_local_data(name, val){
    localStorage.setItem(name, val);
}
// store_local_data("version", "0");



function _fetch_data(location, useLocal) {
    return new Promise(function(resolve, reject) {
        if (useLocal == 1) {
            if (RESOURCE_VERSION == local_ver) {
                var data = load_local_data(location);
                if(data != null && data != undefined){
                    resolve(data);
                }
            }
        }

        // 1. 創建 XMLHttpRequest 對象
        const xhr = new XMLHttpRequest();
        
        // 2. 初始化請求
        xhr.open("GET", location, true); // true 表示異步
        
        // 3. 設置回調函數
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) { // 4 表示請求已完成
                if (xhr.status === 200) { // 200 表示請求成功
                    var data = xhr.responseText;
                    store_local_data(location, data);
                    resolve(data); // 返回結果
                } else {
                    reject('請求失敗，狀態碼：' + xhr.status); // 返回錯誤
                }
            }
        };
        
        // 4. 發送請求
        xhr.send();
    });
}

function fetch_data(location, useLocal, html_id = null){
    _fetch_data(location, useLocal)
        .then(function(data){
            if(html_id != null){
                console.log("Setting "+html_id);
                document.getElementById(html_id).innerHTML = data;
                return;
            }
            return data;
        })
        .catch(function(error){
            console.error(error);
            return null;
        })
}

// Update local storage
var local_ver = load_local_data("version");
if(local_ver != RESOURCE_VERSION || local_ver == null){
    Object.entries(local_resources).forEach(([key, value]) => {
        console.log("Downloading "+key+"...");
        store_local_data(key, fetch_data(key, 0));
    });
    store_local_data("version", RESOURCE_VERSION);
}


$(document).ready(function(){
    Object.entries(local_resources).forEach(([key, value]) => {
        try{
            fetch_data(key, 1, value)
        }catch(e){
            console.error(e);
        }
    });
})