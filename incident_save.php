<?php
session_start();
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

/* ================= SAFE FORM DATA ================= */
$iir_no            = mysqli_real_escape_string($conn, $_POST['iir_no']);
$incident_date     = mysqli_real_escape_string($conn, $_POST['incident_date']);
$unit              = mysqli_real_escape_string($conn, $_POST['unit']);
$section           = mysqli_real_escape_string($conn, $_POST['section']);
$description       = mysqli_real_escape_string($conn, $_POST['description']);
$people_involved   = mysqli_real_escape_string($conn, $_POST['people_involved']);
$area_operator     = mysqli_real_escape_string($conn, $_POST['area_operator']);
$shift_incharge    = mysqli_real_escape_string($conn, $_POST['shift_incharge']);
$maintenance_technician = mysqli_real_escape_string($conn, $_POST['maintenance_technician']);
$injured_condition = mysqli_real_escape_string($conn, $_POST['injured_condition']);
$root_cause        = mysqli_real_escape_string($conn, $_POST['root_cause']);
$recommendations   = mysqli_real_escape_string($conn, $_POST['recommendations']);
$created_by        = mysqli_real_escape_string($conn, $_SESSION['user']);

/* ================= INSERT INCIDENT ================= */
$sql = "
INSERT INTO incident_reports
(iir_no, incident_date, unit, section, description, people_involved,
 area_operator, shift_incharge, maintenance_technician,
 injured_condition, root_cause, recommendations, created_by)
VALUES (
'$iir_no',
'$incident_date',
'$unit',
'$section',
'$description',
'$people_involved',
'$area_operator',
'$shift_incharge',
'$maintenance_technician',
'$injured_condition',
'$root_cause',
'$recommendations',
'$created_by'
)"
;

if (!mysqli_query($conn, $sql)) {
    die("Incident insert failed");
}

$incident_id = mysqli_insert_id($conn);

/* ================= MULTIPLE FILE UPLOAD ================= */
$uploadDir = "var/www/html/compliance/uploads/incident_reports/";
$allowed   = ['pdf','jpg','jpeg','png','doc','docx'];

if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
    die("Upload directory issue");
}

if (!empty($_FILES['attachments']['name'][0])) {

    foreach ($_FILES['attachments']['name'] as $key => $name) {

        if ($_FILES['attachments']['error'][$key] !== 0) {
            continue;
        }

        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            continue;
        }

        // Clean filename
        $safeName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $name);
        $fileName = time() . "_" . rand(1000,9999) . "_" . $safeName;
        $target   = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachments']['tmp_name'][$key], $target)) {

            $relativePath = "var/www/html/compliance/uploads/incident_reports/" . $fileName;

            mysqli_query($conn, "
                INSERT INTO incident_attachments (incident_id, file_path)
                VALUES ($incident_id, '$relativePath')
            ");
        }
    }
}

/* ================= REDIRECT ================= */
header("Location: incident_investigation.php");
exit;
