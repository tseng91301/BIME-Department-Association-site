# About the Local Server
test URL: https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id=2005653179&redirect_uri=https://ntu-bime-linebot.onrender.com/login_callback?authenticator=line&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal
https://access.line.me/oauth2/v2.1/authorize/consent?response_type=code&client_id=2005653179&redirect_uri=https%3A%2F%2Fntu-bime-linebot.onrender.com%2Flogin_callback%3Fauthenticator%3Dline&state=fsaafjwri20ttga0hwpjisg0t5&scope=openid%20profile&nonce=helloWorld&prompt=consent&max_age=3600&ui_locales=zh-TW&bot_prompt=normal#/
## Login Status code:
* 0: Not logged in
* 1: Logged in
* 2: Need to complete account information
* 3: Waiting for Admin approval 
## Setup the Local Server:
### Sqlite database setup:
1. Install sqlite dependencies
```shell
sudo apt-get update
sudo apt-get install sqlite3 libsqlite3-dev php-sqlite3
sudo apt-get install php-sqlite3
```
2. Change configuration of `php.ini` (de-annotation following lines)
`extension=pdo_sqlite`
`extension=sqlite3`
    * to find `php.ini`, use `php --ini` command
3. [Example of SQlite php code](#php-sqlite-usage)

### mySql database setup:
1. Install mySql dependencies
```shell
sudo apt-get install mysql-server
sudo apt install mysql-client
sudo apt install libmysqlclient-dev
```
2. Login into mySql database
    * Default username and password is in key_chain/debian.cnf
    * New added username and password is in key_chain/mysql_account
    * Command:
    `mysql -u <user> -p`
    `create user '<username>'@'localhost' identified by '<password>';`
    `create database <database name>;`
    `grant all privileges on <database_name>.* to '<user>'@'localhost';`
    `flush privileges;`

### install PHP cURL dependencies
1. Install PHP cURL
```shell
sudo apt-get update
sudo apt-get install php-curl
```
## Example code
#### php SQlite usage
```php
<?php
try {
    // 创建或打开一个SQLite数据库文件
    $db = new PDO('sqlite:database.sqlite');

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
```
- [Back to Sqlite database setup](#sqlite-database-setup)
