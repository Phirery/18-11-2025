<?php
header('Content-Type: application/json; charset=utf-8');

// CHO PHÉP FRONTEND localhost:5500
header('Access-Control-Allow-Origin: http://localhost:5500');

// BẮT BUỘC khi dùng fetch() kèm credentials
header('Access-Control-Allow-Credentials: true');

// CHO PHÉP HEADER CLIENT gửi
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// CHO PHÉP METHOD
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

date_default_timezone_set('Asia/Ho_Chi_Minh');

// Browser gửi OPTIONS trước khi gửi request thật
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>