<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Incident Report</title>

<style>
body {
    background:#f4f7f6;
    font-family:"Segoe UI";
}
.form-box {
    max-width:900px;
    margin:40px auto;
    background:#fff;
    padding:35px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}
h2 {
    margin-top:0;
    color:#1b8f4c;
}
label {
    font-weight:600;
    display:block;
    margin-top:18px;
}
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
    background:#1b8f4c;
    color:#fff;
    padding:12px 18px;
    border:none;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
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
    <h2>Incident Investigation Report</h2>
    <a href="incident_investigation.php" title="Back to Dashboard">
        <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
    </a>
</div>
<label>IIR Number</label>
<input name="iir_no" required>

<label>Date & Time of Incident</label>
<input type="datetime-local" name="incident_date" required>

<label>Attach Investigation File (PDF / Image / DOC)</label>
<input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

<label>Unit</label>
<input name="unit">

<label>Section</label>
<input name="section">

<label>Incident Description</label>
<textarea name="description"></textarea>

<label>People Involved</label>
<textarea name="people_involved"></textarea>

<label>Condition of Injured & Action Taken</label>
<textarea name="injured_condition"></textarea>

<label>Root Cause</label>
<textarea name="root_cause"></textarea>

<label>Recommendations & Action Plan</label>
<textarea name="recommendations"></textarea>

<button type="submit">Save Report</button>

</form>
</div>
</body>
</html>
