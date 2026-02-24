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
/* COPY EXACT SAME CSS FROM CREATE PAGE */
body { background:#f4f7f6; font-family:"Segoe UI"; }
.form-box {
    max-width:900px;
    margin:40px auto;
    background:#fff;
    padding:35px;
    border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.1);
}
h2 { margin-top:0; color:#1b8f4c; }
label { font-weight:600; display:block; margin-top:18px; }
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
.tpl-logo { width:42px; cursor:pointer; }

/* ===== Recommendation Table Styling (SAME AS CREATE) ===== */

.recommendation-wrapper { margin-top:20px; overflow-x:auto; }
.recommendation-table {
    width:100%;
    border-collapse:collapse;
    margin-top:12px;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
}
.recommendation-table th {
    background:#1b8f4c;
    color:#fff;
    padding:12px;
    font-size:14px;
    text-align:center;
}
.recommendation-table td {
    border:1px solid #e0e0e0;
    padding:8px;
    vertical-align:top;
    background:#fafafa;
}
.recommendation-table td:first-child,
.recommendation-table th:first-child {
    text-align:center;
    vertical-align:middle;
    font-weight:600;
}
.recommendation-table td:last-child,
.recommendation-table th:last-child {
    text-align:center;
    vertical-align:middle;
}
.recommendation-table textarea,
.recommendation-table input[type="text"],
.recommendation-table input[type="date"],
.recommendation-table select {
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    border:1.5px solid #e2e8f0;
    background:#ffffff;
    font-size:14px;
    transition:all .25s ease;
    box-sizing:border-box;
}
.recommendation-table textarea {
    min-height:90px;
    resize:vertical;
}
.recommendation-table textarea:focus,
.recommendation-table input:focus,
.recommendation-table select:focus {
    border-color:#1b8f4c;
    box-shadow:0 0 0 3px rgba(27,143,76,.15);
    outline:none;
}
.status-pending {
    background:#fff4e5;
    color:#d97706;
    font-weight:600;
}
.status-approved {
    background:#e6f9ef;
    color:#15803d;
    font-weight:600;
}
.add-row-btn {
    margin-top:12px;
    background:#1b8f4c;
    color:#fff;
    padding:10px 16px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}
.delete-btn {
    background:#ef4444;
    color:white;
    padding:6px 8px;
    border-radius:20px;
    cursor:pointer;
    border:none;
    margin-bottom: 18px;
}
.pagination-bar {
    margin-top:14px;
    display:flex;
    justify-content:center;
    gap:12px;
}
.page-btn {
    background:#1b8f4c;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
}
</style>
</head>

<body>

<div class="form-box">
<form method="POST" action="incident_update.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="form-header">
    <h2>Edit Incident Investigation Report</h2>
    <a href="incident_investigation.php">
        <img src="images/tpl.png" class="tpl-logo">
    </a>
</div>

<label>IIR Number</label>
<input name="iir_no" value="<?= $data['iir_no'] ?>" required>

<label>Date & Time of Incident</label>
<input type="datetime-local" name="incident_date"
value="<?= date('Y-m-d\TH:i', strtotime($data['incident_date'])) ?>" required>

<label>Replace Attachment</label>
<input type="file" name="attachment">

<label>Unit where Incident occurred</label>
<input name="unit" value="<?= $data['unit'] ?>">

<label>Section of the Unit where Incident occurred</label>
<input name="section" value="<?= $data['section'] ?>">

<label>Brief Description of the Incident</label>
<textarea name="description"><?= $data['description'] ?></textarea>

<label>People Involved</label>
<textarea name="people_involved"><?= $data['people_involved'] ?></textarea>

<label>Area Operator</label>
<textarea name="area_operator"></textarea>

<label>Shift Incharge</label>
<textarea name="shift_incharge"></textarea>

<label>Maintenance Technician / Engineer</label>
<textarea name="maintenance_technician"></textarea>

<label>Brief Description of the Condition of Injured & Action Taken</label>
<textarea name="injured_condition"><?= $data['injured_condition'] ?></textarea>

<label>Root Cause of the Incident</label>
<textarea name="root_cause"><?= $data['root_cause'] ?></textarea>

<label style="margin-top:30px;font-weight:700;">
Recommendation and action plan to avoid in future with agreed timeline:
</label>

<div class="recommendation-wrapper">
<table class="recommendation-table">
<thead>
<tr>
<th style="width:60px;">S.No</th>
<th>Recommendation & Observations</th>
<th style="width:120px;">Resp</th>
<th style="width:140px;">Target Date</th>
<th>Remarks</th>
<th style="width:120px;">Status</th>
<th style="width:70px;">Action</th>
</tr>
</thead>
<tbody id="tableBody"></tbody>
</table>

<div class="pagination-bar">
<button type="button" class="page-btn" onclick="prevPage()">❮</button>
<span id="pageInfo"></span>
<button type="button" class="page-btn" onclick="nextPage()">❯</button>
</div>

<button type="button" class="add-row-btn" onclick="addRow()">+ Add Row</button>
</div>

<button type="submit">Update Report</button>

</form>
</div>

<script>
let rows = [];
let currentPage = 1;
const rowsPerPage = 10;
const userRole = "admin";

/* LOAD EXISTING ROWS */
<?php
$rq = mysqli_query($conn,"SELECT * FROM incident_recommendations WHERE incident_id=$id");
while($row = mysqli_fetch_assoc($rq)):
?>
rows.push({
id:"<?= $row['id'] ?>",
recommendation:`<?= addslashes($row['recommendation']) ?>`,
resp:`<?= addslashes($row['resp']) ?>`,
target_date:"<?= $row['target_date'] ?>",
remarks:`<?= addslashes($row['remarks']) ?>`,
status:"<?= $row['status'] ?>"
});
<?php endwhile; ?>

function addRow(){
rows.push({id:"",recommendation:"",resp:"",target_date:"",remarks:"",status:"pending"});
renderTable();
}

function deleteRow(index){
rows.splice(index,1);
renderTable();
}

function renderTable(){
const tbody=document.getElementById("tableBody");
tbody.innerHTML="";
const totalPages=Math.ceil(rows.length/rowsPerPage)||1;
const start=(currentPage-1)*rowsPerPage;
const end=start+rowsPerPage;
rows.slice(start,end).forEach((row,i)=>{
const realIndex=start+i;
tbody.innerHTML+=`
<tr>
<td>${realIndex+1}</td>
<td>
<input type="hidden" name="rec_id[]" value="${row.id}">
<textarea name="recommendation[]">${row.recommendation}</textarea>
</td>
<td><input type="text" name="resp[]" value="${row.resp}"></td>
<td><input type="date" name="target_date[]" value="${row.target_date}"></td>
<td><textarea name="remarks[]">${row.remarks}</textarea></td>
<td>
<select name="status[]">
<option value="pending" ${row.status=="pending"?"selected":""}>Pending</option>
<option value="approved" ${row.status=="approved"?"selected":""}>Approved</option>
</select>
</td>
<td><button type="button" class="delete-btn" onclick="deleteRow(${realIndex})">✖</button></td>
</tr>`;
});
document.getElementById("pageInfo").innerText="Page "+currentPage+" of "+totalPages;
}
function nextPage(){currentPage++;renderTable();}
function prevPage(){currentPage--;renderTable();}
renderTable();
</script>

</body>
</html>