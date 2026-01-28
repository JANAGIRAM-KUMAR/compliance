<?php
session_start();
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$attachmentPath = null;

/* ================= FILE UPLOAD ================= */
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {

    $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
    $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type");
    }

    // ABSOLUTE PATH (SERVER)
    $uploadDir = "/var/www/html/compliance/uploads/incident_reports/";

    if (!is_dir($uploadDir)) {
        die("Upload directory missing");
    }

    if (!is_writable($uploadDir)) {
        die("Upload directory not writable");
    }

    // Clean filename
    $safeName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['attachment']['name']);
    $fileName = time() . "_" . $safeName;

    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
        die("File upload failed");
    }

    // SAVE RELATIVE PATH IN DB
    $attachmentPath = "uploads/incident_reports/" . $fileName;
}

/* ================= SAFE DATA ================= */
$iir_no            = mysqli_real_escape_string($conn, $_POST['iir_no']);
$incident_date     = mysqli_real_escape_string($conn, $_POST['incident_date']);
$unit              = mysqli_real_escape_string($conn, $_POST['unit']);
$section           = mysqli_real_escape_string($conn, $_POST['section']);
$description       = mysqli_real_escape_string($conn, $_POST['description']);
$people_involved   = mysqli_real_escape_string($conn, $_POST['people_involved']);
$injured_condition = mysqli_real_escape_string($conn, $_POST['injured_condition']);
$root_cause        = mysqli_real_escape_string($conn, $_POST['root_cause']);
$recommendations   = mysqli_real_escape_string($conn, $_POST['recommendations']);
$created_by        = mysqli_real_escape_string($conn, $_SESSION['user']);

/* ================= INSERT ================= */
$sql = "
INSERT INTO incident_reports
(iir_no, incident_date, unit, section, description, people_involved,
 injured_condition, root_cause, recommendations, attachment, created_by)
VALUES (
'$iir_no',
'$incident_date',
'$unit',
'$section',
'$description',
'$people_involved',
'$injured_condition',
'$root_cause',
'$recommendations',
" . ($attachmentPath ? "'$attachmentPath'" : "NULL") . ",
'$created_by'
)";

mysqli_query($conn, $sql);

header("Location: incident_investigation.php");
exit;
