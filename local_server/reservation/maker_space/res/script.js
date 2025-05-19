$(document).ready(function() {
    function updateTime() {
        let currentTime = new Date();
        let year = currentTime.getUTCFullYear().toString();
        let month = (currentTime.getMonth() + 1).toString().padStart(2, '0');
        let day = currentTime.getDate().toString().padStart(2, '0');
        let hours = currentTime.getHours().toString().padStart(2, '0');
        let minutes = currentTime.getMinutes().toString().padStart(2, '0');
        let seconds = currentTime.getSeconds().toString().padStart(2, '0');
        
        let timeString = 'Current time: <br/>' + year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds;
        $('#time').html(timeString);
    }
    
    // Initial time update
    updateTime();
    
    // Update time every second
    setInterval(updateTime, 1000);
});

function get_book_time() {
    // 取得日期和時間的 input
    var dateInput = document.getElementById('book-date');
    var startTimeInput = document.getElementById('book-start-time');
    var endTimeInput = document.getElementById('book-end-time');

    // 取得 date 和 time 的毫秒數
    var dateTimestamp = dateInput.valueAsNumber; // 日期的毫秒數（自1970年起的毫秒）
    var startTimeTimestamp = startTimeInput.valueAsNumber; // 自午夜起的毫秒
    var endTimeTimestamp = endTimeInput.valueAsNumber;

    var nowdate = new Date();
    now_date_unix = nowdate.getTime();


    if (!isNaN(dateTimestamp) && !isNaN(startTimeTimestamp)) {
        // 創建 Date 物件，並加上時間戳記
        var startDateTime = new Date(dateTimestamp + startTimeTimestamp);
        var endDateTime = new Date(dateTimestamp + endTimeTimestamp);

        // 修正時區偏移
        var timezoneOffset = startDateTime.getTimezoneOffset() * 60000; // 本地時區偏移量

        return {
            "start": startDateTime.getTime() + timezoneOffset,
            "end": endDateTime.getTime() + timezoneOffset,
            "valid": check_time_valid(now_date_unix, startDateTime.getTime() + timezoneOffset, endDateTime.getTime() + timezoneOffset)
        };
        
    }

}

function check_time_valid(c, t1, t2) {
    if(c >= t1){
        return 0;
    }
    if(t2 <= t1) {
        return 0;
    }
    return 1;
}

$(document).ready(function(){
    $("#book-step-1").click(function(){
        // Check whether the form is complete
        if(isNaN(document.getElementById('book-date').valueAsNumber)) {
            alert("請選擇日期");
            return;
        }
        if(isNaN(document.getElementById('book-start-time').valueAsNumber)) {
            alert("請選擇開始時間");
            return;
        }
        if(isNaN(document.getElementById('book-end-time').valueAsNumber)) {
            alert("請選擇結束時間");
            return;
        }
        if(document.getElementById("agree-with-eula").checked == false) {
            alert("請勾選 \"同意使用條款\"");
            return;
        }
        
        var reservation_data = {
            "datetime": get_book_time()
        }
        console.log(reservation_data);
        if(reservation_data['datetime']['valid'] == 0) {
            alert("時間輸入有誤，請檢查");
            return;
        }
        $.ajax({
            type: 'POST',
            url: 'api/book_step_1.php',
            data: {
                'reservation_data': JSON.stringify(reservation_data)
            },
            success: function(response) {
                console.log("Response from server:", response);
                handle_reservation_response(response);
            },
            error: function(xhr, status, error) {
                console.log("Error occurred:", error);
            }
        });
    })
});

function handle_reservation_response(response) {
    if(response.indexOf('[E0]') != -1) {
        alert("您尚未登入系統");
        window.location.href = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal";
    }
    if(response.indexOf('[E1]') != -1){
        alert("JSON decode錯誤");
    }
    if(response.indexOf('[E2]') != -1){
        alert('資料未填寫完全');
    }
    if(response.indexOf('[E3]') != -1){
        alert('mySQL資料庫錯誤');
    }
    if(response.indexOf('[R0]') != -1){
        let userConfirmed = confirm('預約成功，是否跳轉至預約列表？');
        if(userConfirmed) {
            window.location.href = "book_check.html";
        }
    }
    return;
}
