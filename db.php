<?php
$conn = mysqli_connect(
    "172.27.17.136",
    "admin_user",
    "Admin@123",
    "compliance"
);

if (!$conn) {
    die("DB Connection Failed: " . mysqli_connect_error());
}
?>
