<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = $_POST['id'];
mysqli_query($conn,"UPDATE incident_reports SET status='approved' WHERE id=$id");

header("Location: incident_investigation.php");
exit;
