<?php
// RDS Database Configuration (Professional Standard: Use environment variables in production)
$host = "database-2tier.c21muigc2r4y.us-east-1.rds.amazonaws.com"; // Misal ke tor par: database-2tier.c123456789.us-east-1.rds.amazonaws.com
$username = "admin";
$password = "Mian$6767";
$database = "database-2tier"; // Ya jo database name aapne rakha ho

// MySQL Connection Establish karna
$conn = new mysqli($host, $username, $password, $database);

// Connection Check karna
if ($conn->connect_error) {
    die("<h3 style='color: red;'>Database Connection Failed: </h3> " . $conn->connect_error);
}

echo "<h2 style='color: green;'>Success! EC2 Web Server is successfully connected to RDS MySQL Database!</h2>";

// Table data fetch karne ka sample query (agar table mojood ho)
$sql = "SELECT NOW() as current_time_db";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo "<p>Database Server Time: " . $row['current_time_db'] . "</p>";
}

$conn->close();
?>