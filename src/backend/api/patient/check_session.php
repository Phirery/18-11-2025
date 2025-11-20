<?php
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');

echo json_encode([
    'success' => true,
    'logged_in' => isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false,
    'maBenhNhan' => $_SESSION['maBenhNhan'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'vai_tro' => $_SESSION['vai_tro'] ?? null,
    'all_session' => $_SESSION
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>