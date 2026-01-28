<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    echo "<pre>";
    print_r($_FILES['file']);
    echo "</pre>";

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
        die("? Upload error code: " . ($_FILES['file']['error'] ?? 'No file'));
    }

    // ABSOLUTE SERVER PATH
    $uploadDir = "/var/www/html/compliance/uploads/incident_reports/";

    // Check folder exists
    if (!is_dir($uploadDir)) {
        die("? Upload directory does not exist");
    }

    // Check writable permission
    if (!is_writable($uploadDir)) {
        die("? Upload directory is NOT writable");
    }

    // Generate safe filename
    $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "_", $_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    // Move file
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        echo "<p style='color:green;font-weight:bold'>? File uploaded successfully</p>";
        echo "<p>Saved as:</p>";
        echo "<code>/compliance/uploads/incident_reports/$fileName</code>";
    } else {
        echo "<p style='color:red;font-weight:bold'>? move_uploaded_file() failed</p>";
    }
}
?>

<hr>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file" required>
    <button type="submit">Upload Test</button>
</form>
