function convertUnixTimestamp(unixTimestamp, second_to_millis = false) {
    // 創建 Date 物件
    unixTimestamp = parseInt(unixTimestamp);
    if(second_to_millis) {
        unixTimestamp *= 1000;
    }
    let date = new Date(unixTimestamp);

    // 獲取日期部分
    let year = date.getFullYear();
    let month = (date.getMonth() + 1).toString().padStart(2, '0'); // 月份從 0 開始，所以需要 +1
    let day = date.getDate().toString().padStart(2, '0');

    // 獲取時間部分
    let hours = date.getHours().toString().padStart(2, '0');
    let minutes = date.getMinutes().toString().padStart(2, '0');
    let seconds = date.getSeconds().toString().padStart(2, '0');

    // 返回日期和時間
    return {
        'date': `${year}-${month}-${day}`,
        'time': `${hours}:${minutes}:${seconds}`
    };
}

function unix_TS_to_normal(stamp, second_to_millis = false) {
    stamp = parseInt(stamp);
    if(second_to_millis) {
        stamp *= 1000;
    }
    let date = new Date(stamp);

    // 獲取日期部分
    let year = date.getFullYear();
    let month = (date.getMonth() + 1).toString().padStart(2, '0'); // 月份從 0 開始，所以需要 +1
    let day = date.getDate().toString().padStart(2, '0');

    // 獲取時間部分
    let hours = date.getHours().toString().padStart(2, '0');
    let minutes = date.getMinutes().toString().padStart(2, '0');
    let seconds = date.getSeconds().toString().padStart(2, '0');

    // 返回日期和時間
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

}

function close_float() {
    $("#floating-window").addClass("floating-window-hidden");
    return;
}

var book_status_code_info = ['預約成立', '正在審核', '正在安排管理人員', '預約取消', '預約被禁止', '預約未到', 'Show up', '預約被管理員取消']