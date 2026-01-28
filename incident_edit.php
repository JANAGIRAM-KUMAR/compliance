<?php
session_start();
include "db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$id = intval($_GET['id']);

/* ================= FETCH INCIDENT ================= */
$q = mysqli_query($conn, "SELECT * FROM incident_reports WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Incident not found");
}

if ($data['status'] === 'approved') {
    die("Approved reports cannot be edited");
}

/* ================= FETCH ATTACHMENTS ================= */
$attachments = [];
$aq = mysqli_query($conn, "SELECT * FROM incident_attachments WHERE incident_id=$id");
while ($row = mysqli_fetch_assoc($aq)) {
    $attachments[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Incident Report</title>

<style>
body { background:#f4f7f6; font-family:"Segoe UI"; }

.form-box {
    max-width:900px;
    margin:40px auto;
    background:#fff;
    padding:35px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}

label { font-weight:600; margin-top:16px; display:block; }

input, textarea {
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:8px;
    border:1px solid #ccc;
}

textarea { min-height:90px; }

button {
    margin-top:25px;
    background:#1976d2;
    color:#fff;
    padding:12px 20px;
    border:none;
    border-radius:8px;
    font-weight:600;
    cursor:pointer;
}

.form-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.tpl-logo { width:42px; cursor:pointer; }

.file-box {
    background:#fafafa;
    padding:12px;
    border-radius:10px;
    margin-top:10px;
}

.file-name { font-weight:600; margin-bottom:8px; }

.file-actions {
    display:flex;
    gap:10px;
}

.btn {
    min-width:120px;
    height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    font-weight:600;
    text-decoration:none;
    border:none;
    cursor:pointer;
}

.btn-view {
    background:#e3f2fd;
    color:#0d47a1;
}

.btn-delete {
    background:#fdecea;
    color:#c62828;
    margin-top: 0px;
}
</style>
</head>

<body>

<div class="form-box">

    <div class="form-header">
        <h2>Edit Incident Investigation Report</h2>
        <a href="incident_investigation.php">
            <img src="images/tpl.png" class="tpl-logo">
        </a>
    </div>

    <!-- ================= EXISTING ATTACHMENTS (OUTSIDE MAIN FORM) ================= -->
    <?php if (!empty($attachments)): ?>
        <label>Existing Attachments</label>

        <?php foreach ($attachments as $file): ?>
            <div class="file-box">
                <div class="file-name"><?= basename($file['file_path']) ?></div>

                <div class="file-actions">
                    <a href="<?= $file['file_path'] ?>" target="_blank" class="btn btn-view">
                        View
                    </a>

                    <form method="post" action="attachment_delete.php"
                          onsubmit="return confirm('Delete this file?');">
                        <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                        <input type="hidden" name="incident_id" value="<?= $id ?>">
                        <button type="submit" class="btn btn-delete">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ================= MAIN UPDATE FORM ================= -->
    <form method="post" action="incident_update.php" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= $id ?>">

        <label>IIR Number</label>
        <input name="iir_no" value="<?= htmlspecialchars($data['iir_no']) ?>" required>

        <label>Date & Time of Incident</label>
        <input type="datetime-local" name="incident_date"
               value="<?= date('Y-m-d\TH:i', strtotime($data['incident_date'])) ?>" required>

        <label>Unit where incident occured:</label>
        <input name="unit" value="<?= htmlspecialchars($data['unit']) ?>">

        <label>Section of the Unit where incident occured:</label>
        <input name="section" value="<?= htmlspecialchars($data['section']) ?>">

        <label>Brief description of incident:</label>
        <textarea name="description"><?= htmlspecialchars($data['description']) ?></textarea>

        <label>People Involved</label>
        <textarea name="people_involved"><?= htmlspecialchars($data['people_involved']) ?></textarea>

        <label>Area Operator</label>
        <input name="area_operator" value="<?= htmlspecialchars($data['area_operator']) ?>">

        <label>Shift Incharge</label>
        <input name="shift_incharge" value="<?= htmlspecialchars($data['shift_incharge']) ?>">
        <label>Maintenance Technician / Engineer</label>
        <input name="maintenance_technician" value="<?= htmlspecialchars($data['maintenance_technician']) ?>">

        <label>Condition of Injured & Action Taken</label>
        <textarea name="injured_condition"><?= htmlspecialchars($data['injured_condition']) ?></textarea>

        <label>Root Cause of Incident</label>
        <textarea name="root_cause"><?= htmlspecialchars($data['root_cause']) ?></textarea>

        <label>Add More Attachments</label>
        <input type="file" name="attachments[]" multiple
               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

        <label>Recommendations & Action Plan</label>
        <textarea name="recommendations"><?= htmlspecialchars($data['recommendations']) ?></textarea>

        <button type="submit">Save All Changes</button>

    </form>
</div>

</body>
</html>
