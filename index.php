<?php
$host = "10.0.5.97";
$username = "admin";
$password = "Mian$6767";
$database = "database-2tier";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
echo "Success! EC2 Web Server is successfully connected to RDS MySQL Database!";
?>