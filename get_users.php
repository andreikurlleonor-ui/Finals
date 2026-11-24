<?php
require "db.php";

header("Content-Type: application/json");

$result = $conn->query("SELECT id, name, email FROM users");
echo json_encode($result->fetch_all(MYSQLI_ASSOC));