<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$attachmentPath = null;

if (!empty($_FILES['attachment']['name'])) {

    $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
    $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        die("Invalid file type");
    }

    $uploadDir = "uploads/incident_reports/";
    $fileName = time() . "_" . basename($_FILES['attachment']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {
        $attachmentPath = $targetPath;
    }
}

$sql = "INSERT INTO incident_reports
(iir_no, incident_date, unit, section, description, people_involved,
 injured_condition, root_cause, recommendations, attachment, created_by)
VALUES (
'{$_POST['iir_no']}',
'{$_POST['incident_date']}',
'{$_POST['unit']}',
'{$_POST['section']}',
'{$_POST['description']}',
'{$_POST['people_involved']}',
'{$_POST['injured_condition']}',
'{$_POST['root_cause']}',
'{$_POST['recommendations']}',
'$attachmentPath',
'{$_SESSION['user']}'
)";

mysqli_query($conn, $sql);

header("Location: incident_investigation.php");
exit;
