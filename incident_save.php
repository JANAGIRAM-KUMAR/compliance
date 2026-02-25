<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$allowed = ['pdf','jpg','jpeg','png','doc','docx'];
$uploadDir = "uploads/incident_reports/";

if (!empty($_FILES['attachments']['name'][0])) {

    foreach ($_FILES['attachments']['name'] as $key => $fileName) {

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            continue; // skip invalid files instead of stopping everything
        }

        $newName = time() . "_" . $key . "_" . basename($fileName);
        $targetPath = $uploadDir . $newName;

        if (move_uploaded_file($_FILES['attachments']['tmp_name'][$key], $targetPath)) {

            $insertAttachment = "INSERT INTO incident_attachments
            (incident_id, file_path)
            VALUES
            ('$incident_id', '$targetPath')";

            mysqli_query($conn, $insertAttachment);
        }
    }
}

$sql = "INSERT INTO incident_reports
(iir_no, incident_date, unit, section, description, people_involved, area_operator, shift_incharge, maintenance_technician,
 injured_condition, root_cause, created_by)
VALUES (
'{$_POST['iir_no']}',
'{$_POST['incident_date']}',
'{$_POST['unit']}',
'{$_POST['section']}',
'{$_POST['description']}',
'{$_POST['people_involved']}',
'{$_POST['area_operator']}',
'{$_POST['shift_incharge']}',
'{$_POST['maintenance_technician']}',
'{$_POST['injured_condition']}',
'{$_POST['root_cause']}',
'{$_SESSION['user']}'
)";

mysqli_query($conn, $sql);

$incident_id = mysqli_insert_id($conn);


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
