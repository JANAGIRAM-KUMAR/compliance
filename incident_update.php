<?php
session_start();
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = intval($_POST['id']);

/* ================= STATUS CHECK ================= */
$q = mysqli_query($conn, "SELECT status FROM incident_reports WHERE id=$id");
$row = mysqli_fetch_assoc($q);

if (!$row) {
    die("Incident not found");
}

if ($row['status'] === 'approved') {
    die("Approved reports cannot be edited");
}

/* ================= SAFE DATA ================= */
$iir_no            = mysqli_real_escape_string($conn, $_POST['iir_no']);
$incident_date     = mysqli_real_escape_string($conn, $_POST['incident_date']);
$unit              = mysqli_real_escape_string($conn, $_POST['unit']);
$section           = mysqli_real_escape_string($conn, $_POST['section']);
$description       = mysqli_real_escape_string($conn, $_POST['description']);
$people_involved   = mysqli_real_escape_string($conn, $_POST['people_involved']);
$area_operator          = mysqli_real_escape_string($conn, $_POST['area_operator']);
$shift_incharge         = mysqli_real_escape_string($conn, $_POST['shift_incharge']);
$maintenance_technician = mysqli_real_escape_string($conn, $_POST['maintenance_technician']);
$injured_condition = mysqli_real_escape_string($conn, $_POST['injured_condition']);
$root_cause        = mysqli_real_escape_string($conn, $_POST['root_cause']);
$recommendations   = mysqli_real_escape_string($conn, $_POST['recommendations']);

/* ================= UPDATE INCIDENT ================= */
$sql = "
UPDATE incident_reports SET
    iir_no='$iir_no',
    incident_date='$incident_date',
    unit='$unit',
    section='$section',
    description='$description',
    people_involved='$people_involved',
    area_operator='$area_operator',
    shift_incharge='$shift_incharge',
    maintenance_technician='$maintenance_technician',
    injured_condition='$injured_condition',
    root_cause='$root_cause',
    recommendations='$recommendations'
    WHERE id=$id
";

mysqli_query($conn, $sql);

/* ================= ADD NEW ATTACHMENTS ================= */
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

        $safeName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $name);
        $fileName = time() . "_" . rand(1000,9999) . "_" . $safeName;
        $target   = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachments']['tmp_name'][$key], $target)) {

            $relativePath = "var/www/html/compliance/uploads/incident_reports/" . $fileName;

            mysqli_query($conn, "
                INSERT INTO incident_attachments (incident_id, file_path)
                VALUES ($id, '$relativePath')
            ");
        }
    }
}

/* ================= REDIRECT ================= */
header("Location: incident_view.php?id=$id");
exit;
