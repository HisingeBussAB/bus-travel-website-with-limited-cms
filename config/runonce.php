<?php
include 'config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
  print_r($conn);
};

$default_login = "admin";
$default_pwd = password_hash("12345", PASSWORD_DEFAULT);

$sql = "CREATE TABLE " . $table_prefix . "logins (
    id int NOT NULL PRIMARY KEY,
    username CHAR(50),
    pwd CHAR(255));";

echo  "<br><br>" . $sql . "<br><br>";
$result = $conn->query($sql);
print_r($conn);

$sql = "INSERT INTO " . $table_prefix . "logins (
    id,
    username,
    pwd)
    VALUES (
    0,'" .
    $default_login . "','" .
    $default_pwd . "');";
echo "<br><br>" . $sql . "<br><br>";
$result = $conn->query($sql);
print_r($conn);

echo "<br><br>Default user set to admin and default password set to 12345, or fail, look at raw SQL response above. Installation is not supported.";

$conn->close();

?>
