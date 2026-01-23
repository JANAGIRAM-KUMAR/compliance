<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_POST['id'];

$q = mysqli_query($conn,"SELECT status FROM incident_reports WHERE id=$id");
$row = mysqli_fetch_assoc($q);

if ($row['status'] === 'approved') {
    die("Approved reports cannot be edited");
}

$attachmentSQL = "";

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
        $attachmentSQL = ", attachment='$targetPath'";
    }
}

$sql = "UPDATE incident_reports SET
iir_no='{$_POST['iir_no']}',
incident_date='{$_POST['incident_date']}',
unit='{$_POST['unit']}',
section='{$_POST['section']}',
description='{$_POST['description']}',
people_involved='{$_POST['people_involved']}',
injured_condition='{$_POST['injured_condition']}',
root_cause='{$_POST['root_cause']}',
recommendations='{$_POST['recommendations']}'
$attachmentSQL
WHERE id=$id";

mysqli_query($conn, $sql);

header("Location: incident_view.php?id=$id");
exit;
