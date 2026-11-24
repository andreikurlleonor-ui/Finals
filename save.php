<?php
require "db.php";
header("Content-Type: application/json");

$name  = $_POST["name"]  ?? "";
$email = $_POST["email"] ?? "";

$stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $email);
$stmt->execute();

echo json_encode(["success" => true]);
