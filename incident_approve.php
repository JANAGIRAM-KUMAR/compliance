<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

mysqli_query($conn,"UPDATE incident_reports SET status='approved' WHERE id={$_POST['id']}");
header("Location: incident_investigation.php");
exit;
