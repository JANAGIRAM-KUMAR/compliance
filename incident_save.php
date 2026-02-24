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
 injured_condition, root_cause, created_by)
VALUES (
'{$_POST['iir_no']}',
'{$_POST['incident_date']}',
'{$_POST['unit']}',
'{$_POST['section']}',
'{$_POST['description']}',
'{$_POST['people_involved']}',
'{$_POST['injured_condition']}',
'{$_POST['root_cause']}',
'{$_SESSION['user']}'
)";

mysqli_query($conn, $sql);

$incident_id = mysqli_insert_id($conn);

// Save attachment in separate table
if ($attachmentPath !== null) {

    $insertAttachment = "INSERT INTO incident_attachments
    (incident_id, file_path)
    VALUES
    ('$incident_id', '$attachmentPath')";

    mysqli_query($conn, $insertAttachment);
}

if (!empty($_POST['recommendation'])) {

    foreach ($_POST['recommendation'] as $key => $value) {

        $rec = $_POST['recommendation'][$key];
        $resp = $_POST['resp'][$key];
        $date = $_POST['target_date'][$key];
        $remarks = $_POST['remarks'][$key];

        $status = "pending"; // default

        $insertRec = "INSERT INTO incident_recommendations
        (incident_id, recommendation, resp, target_date, remarks, status)
        VALUES (
        '$incident_id',
        '$rec',
        '$resp',
        '$date',
        '$remarks',
        '$status'
        )";

        mysqli_query($conn, $insertRec);
    }
}

header("Location: incident_investigation.php");
exit;
