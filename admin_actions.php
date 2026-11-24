<?php
session_start();
require "db.php";
header("Content-Type: application/json");

// 1. Block non-admins from calling this API
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

// 2. GET Request: Return all users
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $conn->query("SELECT id, name, email, admin AS role FROM users ORDER BY id ASC");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    exit;
}

// 3. POST Request: Promote/Demote or Delete a user
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data["action"] ?? "";
    $targetId = (int)($data["user_id"] ?? 0);

    // Prevent admin from accidentally modifying or deleting their own account
    if ($targetId === (int)$_SESSION["user_id"]) {
        http_response_code(400);
        echo json_encode(["error" => "You cannot modify your own admin account."]);
        exit;
    }

    if ($action === "toggle_role") {
        $newRole = ($data["current_role"] === "admin") ? "user" : "admin";
        $stmt = $conn->prepare("UPDATE users SET admin = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $targetId);
        $stmt->execute();
        echo json_encode(["success" => true]);
        exit;
    }

    if ($action === "delete") {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $targetId);
        $stmt->execute();
        echo json_encode(["success" => true]);
        exit;
    }
}
