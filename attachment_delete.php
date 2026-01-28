<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

if (!isset($_POST['file_id'], $_POST['incident_id'])) {
    die("Invalid request");
}

$file_id     = intval($_POST['file_id']);
$incident_id = intval($_POST['incident_id']);

/* ================= FETCH FILE ================= */
$q = mysqli_query($conn, "
    SELECT file_path 
    FROM incident_attachments 
    WHERE id = $file_id
");

$file = mysqli_fetch_assoc($q);

if (!$file) {
    die("File not found");
}

/* ================= DELETE FILE FROM SERVER ================= */
$fullPath = "/var/www/html/compliance/" . $file['file_path'];

if (file_exists($fullPath)) {
    unlink($fullPath);
}

/* ================= DELETE FROM DB ================= */
mysqli_query($conn, "
    DELETE FROM incident_attachments 
    WHERE id = $file_id
");

/* ================= REDIRECT BACK ================= */
header("Location: incident_edit.php?id=$incident_id");
exit;
