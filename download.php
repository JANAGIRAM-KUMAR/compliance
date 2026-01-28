<?php
session_start();
include "db.php";

if (!isset($_SESSION['role'])) {
    die("Unauthorized");
}

$id = intval($_GET['id']);

$q = mysqli_query($conn, "SELECT attachment FROM incident_reports WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (!$data || empty($data['attachment'])) {
    die("File not found");
}

$filePath = $data['attachment'];

if (!file_exists($filePath)) {
    die("File does not exist on server");
}

// Force download headers
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Length: ' . filesize($filePath));
header('Pragma: public');
header('Cache-Control: must-revalidate');

readfile($filePath);
exit;
