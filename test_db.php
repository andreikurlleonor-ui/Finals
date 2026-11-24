<?php
$conn = new mysqli("localhost", "root", "admin", "fianls.db", 3307);

if ($conn->connect_error) {
    echo "Failed: " . $conn->connect_error;
} else {
    echo "Connected to MySQL successfully!";
}