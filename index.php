<?php
$host = "database-2tier.c21muigc2r4y.us-east-1.rds.amazonaws.com";
$username = "admin";
$password = "Mian$6767";
$database = "database-2tier";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
echo "Success! EC2 Web Server is successfully connected to RDS MySQL Database!";
?>