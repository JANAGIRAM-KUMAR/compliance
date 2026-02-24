<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$rec_id = $_POST['rec_id'];
$incident_id = $_POST['incident_id'];

/* 1️⃣ Approve that recommendation */
mysqli_query($conn, "UPDATE incident_recommendations 
                     SET status='approved' 
                     WHERE id=$rec_id");

/* 2️⃣ Check if any pending recommendations remain */
$check = mysqli_query($conn, "SELECT * FROM incident_recommendations 
                              WHERE incident_id=$incident_id 
                              AND status='pending'");

if (mysqli_num_rows($check) == 0) {
    /* 3️⃣ If none pending → approve main report */
    mysqli_query($conn, "UPDATE incident_reports 
                         SET status='approved' 
                         WHERE id=$incident_id");
}

/* 4️⃣ Redirect back */
header("Location: incident_view.php?id=$incident_id");
exit();
?>