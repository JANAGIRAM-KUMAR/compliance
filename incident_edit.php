<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = $_GET['id'];
$q = mysqli_query($conn,"SELECT * FROM incident_reports WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if ($data['status'] === 'approved') {
    die("Approved reports cannot be edited");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Incident Report</title>
<style>
body { background:#f4f7f6; font-family:"Segoe UI"; }
.form-box {
    max-width:900px; margin:40px auto;
    background:#fff; padding:35px;
    border-radius:14px; box-shadow:0 12px 30px rgba(0,0,0,.1);
}
label { font-weight:600; margin-top:16px; display:block; }
input, textarea {
    width:100%; padding:12px; margin-top:6px;
    border-radius:8px; border:1px solid #ccc;
}
textarea { min-height:90px; }
button {
    margin-top:25px;
    background:#1976d2; color:#fff;
    padding:12px 20px; border:none;
    border-radius:8px; font-weight:600;
    cursor:pointer;
}
.form-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.tpl-logo {
    width:42px;
    height:auto;
    cursor:pointer;
}
</style>
</head>

<body>
<div class="form-box">
<div class="form-header">
    <h2>Edit Incident Investigation Report</h2>
    <a href="incident_investigation.php" title="Back to Dashboard">
        <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
    </a>
</div>

<form method="post" action="incident_update.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<label>IIR Number</label>
<input name="iir_no" value="<?= $data['iir_no'] ?>" required>

<label>Date & Time of Incident</label>
<input type="datetime-local" name="incident_date"
 value="<?= date('Y-m-d\TH:i', strtotime($data['incident_date'])) ?>" required>

<label>Replace Attachment (optional)</label>
<input type="file" name="attachment">

<label>Unit</label>
<input name="unit" value="<?= $data['unit'] ?>">

<label>Section</label>
<input name="section" value="<?= $data['section'] ?>">

<label>Incident Description</label>
<textarea name="description"><?= $data['description'] ?></textarea>

<label>People Involved</label>
<textarea name="people_involved"><?= $data['people_involved'] ?></textarea>

<label>Condition of Injured & Action Taken</label>
<textarea name="injured_condition"><?= $data['injured_condition'] ?></textarea>

<label>Root Cause</label>
<textarea name="root_cause"><?= $data['root_cause'] ?></textarea>

<label>Recommendations & Action Plan</label>
<textarea name="recommendations"><?= $data['recommendations'] ?></textarea>

<button>Save All Changes</button>

</form>
</div>
</body>
</html>
