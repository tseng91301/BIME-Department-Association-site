var book_progress_data = {};
var book_history_data = {};

$(document).ready(function(){
    $.ajax({
        type: 'GET',
        url: 'api/book_check.php',
        dataType: 'json',
        data: {
            'c': "progress"
        },
        success: function(response) {
            console.log("Response from server:", response);
            if(response['result'] == "error") {
                handle_error(response['code']);
                return;
            }
            book_progress_data = response['data'];
            dump_reservation_progress_table(response['data']);
        },
        error: function(xhr, status, error) {
            console.log("Error occurred:", error);
        }
    });
    $.ajax({
        type: 'GET',
        url: 'api/book_check.php',
        dataType: 'json',
        data: {
            'c': "history"
        },
        success: function(response) {
            console.log("Response from server:", response);
            if(response['result'] == "error") {
                handle_error(response['code']);
                return;
            }
            book_history_data = response['data'];
            dump_reservation_history_table(response['data']);
        },
        error: function(xhr, status, error) {
            console.log("Error occurred:", error);
        }
    });
});

function handle_error(e){
    if(e.indexOf('[E0]') != -1) {
        alert("您尚未登入系統");
        window.location.href = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal";
    }
    if(e.indexOf('[E1]') != -1){
        alert("JSON decode錯誤");
    }
    if(e.indexOf('[E2]') != -1){
        alert('資料未填寫完全');
    }
    if(e.indexOf('[E3]') != -1){
        alert('mySQL資料庫錯誤');
    }
}

function dump_reservation_progress_table(data) {
    if(data == null) {
        return;
    }
    var l = data.length;
    var output_html = "<tr><td>日期</td><td>開始時間</td><td>結束時間</td><td>狀態</td><td>操作</td></tr>";
    for(a = l-1;a>=0;a--){
        output_html += "<tr>";
        var start_time_obj = convertUnixTimestamp(data[a]['start_time'], true);
        var end_time_obj = convertUnixTimestamp(data[a]['end_time'], true);
        var date = start_time_obj['date'];
        var start_time = start_time_obj['time'];
        var end_time = end_time_obj['time'];
        output_html += "<td>" + date + "</td><td id='start-time-"+a+"'>" + start_time + "</td><td id='end-time-"+a+"'>" + end_time + "</td><td>";

        var status = parseInt(data[a]['status']);
        output_html += book_status_code_info[status];
        output_html += "</td>";

        var opr_html = ""
        if(status == 0) {
            opr_html = '<button class="btn show-barcode-btn btn-normal" id="show-barcode-' + a + '" onclick="show_qrP(' + a + ')">顯示QR碼</button>';
        }
        opr_html += '<button class="btn cancel-reservation-btn btn-danger" id="cancel-' + a + '" onclick="cancel_reservation(' + a + ')">取消預約</button>';

        output_html += "<td>"+opr_html+"</td>";
        output_html += "</tr>";
        output_html += "\n";
    }
    $("#book-record-progress-tbody").html(output_html);
}  

function dump_reservation_history_table(data) {
    if(data == null) {
        return;
    }
    var l = data.length;
    var output_html = "<tr><td>日期</td><td>開始時間</td><td>結束時間</td><td>狀態</td></tr>";
    for(a = l-1;a>=0;a--){
        output_html += "<tr>";
        var start_time_obj = convertUnixTimestamp(data[a]['start_time'], true);
        var end_time_obj = convertUnixTimestamp(data[a]['end_time'], true);
        var date = start_time_obj['date'];
        var start_time = start_time_obj['time'];
        var end_time = end_time_obj['time'];
        output_html += "<td>" + date + "</td><td>" + start_time + "</td><td>" + end_time + "</td><td>";

        var status = parseInt(data[a]['status']);
        output_html += book_status_code_info[status];
        output_html += "</td>";
        output_html += "</tr>";
        output_html += "\n";
    }
    $("#book-record-history-tbody").html(output_html);
}  

function show_qrP(id, show = true) {
    var show_html = $("#qr-code-spawner-html");
    // 清空之前的 QR 內容
    $("#book-qrP-qr").empty();
    var url = "https://blessed-dogfish-morally.ngrok-free.app/admin_page/reservation/maker_space/checkin.php?rid=";
    $("#book-qrP-rid").html(book_progress_data[id]['rid']);
    $("#book-qrP-real-name").html(book_progress_data[id]['booker_real_name']);
    $("#book-qrP-student-id").html(book_progress_data[id]['booker_student_id']);
    $("#book-qrP-start-time").html(unix_TS_to_normal(book_progress_data[id]['start_time'], true));
    $("#book-qrP-end-time").html(unix_TS_to_normal(book_progress_data[id]['end_time'], true));
    if(show) {
        $("#floating-window").html(show_html.html());
        $("#floating-window").removeClass("floating-window-hidden");
    }
    
    var qrcode = new QRCode(document.getElementById("book-qrP-qr"), {
        text: url + book_progress_data[id]['rid'],  // 要生成 QR 碼的內容
        width: 150,  // QR 碼的寬度
        height: 150,  // QR 碼的高度
        colorDark : "#000000",  // QR 碼深色部分
        colorLight : "#ffffff", // QR 碼淺色部分
        correctLevel : QRCode.CorrectLevel.H  // 錯誤修正等級
    });
    setTimeout(() => {
        var d = $("#book-qrP-qr");
        var img_found = d.find("img")[0];
        if(img_found.style.display == "none") {
            console.log("Regenerating QRcode...");
            show_qrP(id, false); // 若未成功生成，則再生成一次
        }
    }, 200);
    
}

function cancel_reservation(id) {
    var cancel_confirm = confirm("您真的要取消預約嗎？");
    if(cancel_confirm) {
        $.ajax({
            type: 'POST',
            url: 'api/book_check.php',
            dataType: 'json', // 預期回傳 JSON 格式
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8', // 確保使用表單格式
            data: {
                'o': "cancel",
                'rid': book_progress_data[id]['rid']
            },
            success: function(response) {
                console.log("Response from server:", response);
                alert("取消成功！");
                location.reload();
            },
            error: function(xhr, status, error) {
                console.log("Error occurred:", error);
            }
        });
    }
}