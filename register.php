<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed."]);
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

if ($name === "" || $email === "" || $password === "" || $confirmPassword === "") {
    http_response_code(400);
    echo json_encode(["error" => "Please complete all fields."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Enter a valid email address."]);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["error" => "Your password must have at least 8 characters."]);
    exit;
}

if ($password !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(["error" => "Your passwords do not match."]);
    exit;
}

try {
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        http_response_code(409);
        echo json_encode(["error" => "An account with that email already exists."]);
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $name, $email, $passwordHash);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (mysqli_sql_exception $exception) {
    error_log("Account registration failed: " . $exception->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Could not create the account. Please try again."]);
}
