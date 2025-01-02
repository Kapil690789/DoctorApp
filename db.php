<?php
$host = "sql12.freesqldatabase.com"; // Host from your free SQL database
$username = "sql12755220";          // Your username
$password = "895MLdUrqA";           // Your password
$dbname = "sql12755220";  

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
