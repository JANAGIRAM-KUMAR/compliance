<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$attId = intval($_GET['id']);
$incidentId = intval($_GET['incident_id']);

// Get file path
$q = mysqli_query($conn,"SELECT file_path FROM incident_attachments WHERE id=$attId");
$data = mysqli_fetch_assoc($q);

if($data){
    $filePath = $data['file_path'];

    // Delete physical file
    if(file_exists($filePath)){
        unlink($filePath);
    }

    // Delete DB record
    mysqli_query($conn,"DELETE FROM incident_attachments WHERE id=$attId");
}

header("Location: incident_edit.php?id=$incidentId");
exit;
?>