<?php
include_once $_SERVER['DOCUMENT_ROOT']."/settings.php";
try {
    // 创建或打开一个SQLite数据库文件
    $db = new PDO('sqlite:'.$database_root.'database.sqlite');

    // 设置错误模式
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 创建一个表
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        name TEXT,
        email TEXT
    )");

    // 插入数据
    $db->exec("INSERT INTO users (name, email) VALUES ('John Doe', 'john@example.com')");
    $db->exec("INSERT INTO users (name, email) VALUES ('Jane Doe', 'jane@example.com')");

    // 查询数据
    $result = $db->query("SELECT * FROM users");
    foreach ($result as $row) {
        echo "ID: " . $row['id'] . "\n";
        echo "Name: " . $row['name'] . "\n";
        echo "Email: " . $row['email'] . "\n";
    }

    // 更新数据
    $db->exec("UPDATE users SET email = 'john.doe@example.com' WHERE name = 'John Doe'");

    // 删除数据
    $db->exec("DELETE FROM users WHERE name = 'Jane Doe'");

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
