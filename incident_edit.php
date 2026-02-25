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
.form-header {
    display:grid;
    grid-template-columns: 1fr auto 1fr;
    align-items:center;
    margin-bottom:25px;
    padding-bottom:15px;
    border-bottom:2px solid #e5e7eb;
}

.header-left {
    justify-self:start;
}

.header-right {
    justify-self:end;
}
.back-btn {
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 16px;
    border-radius:8px;
    background:#f3f4f6;
    color:#1b8f4c;
    text-decoration:none;
    font-weight:600;
    font-size:14px;
    border:1px solid #d1d5db;
    transition:all .25s ease;
}

.back-btn .arrow {
    font-size:16px;
    transition:transform .25s ease;
}

.back-btn:hover {
    background:#1b8f4c;
    color:white;
    border-color:#1b8f4c;
    box-shadow:0 4px 12px rgba(27,143,76,.25);
}

.back-btn:hover .arrow {
    transform:translateX(-4px);
}
/* ===== Attachment Styling ===== */

.attachment-list {
    margin-top: 10px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.file-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f3fdf7;
    padding: 8px 14px;
    border-radius: 25px;
    border: 1px solid #d1fae5;
    box-shadow: 0 4px 10px rgba(0,0,0,.05);
    transition: all .2s ease;
}

.file-badge:hover {
    background: #e6f9ef;
}

.file-badge a {
    text-decoration: none;
    font-weight: 600;
    color: #1b8f4c;
    font-size: 14px;
}

.delete-attach {
    background: #ef4444;
    color: white !important;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: .2s;
}

.delete-attach:hover {
    background: #dc2626;
    transform: scale(1.1);
}
</style>
</head>

<body>

<div class="form-box">
<form method="POST" action="incident_update.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $id ?>">

<div class="form-header">

    <div class="header-left">
        <a href="incident_investigation.php" class="back-btn">
            <span class="arrow">←</span>
            Back
        </a>
    </div>

    <h2>Edit Incident Investigation Report</h2>

    <div class="header-right">
        <a href="incident_investigation.php">
            <img src="images/tpl.png" class="tpl-logo">
        </a>
    </div>

</div>

<label>IIR Number</label>
<input name="iir_no" value="<?= $data['iir_no'] ?>" required>

<label>Date & Time of Incident</label>
<input type="datetime-local" name="incident_date"
value="<?= date('Y-m-d\TH:i', strtotime($data['incident_date'])) ?>" required>

<label>Add Attachments</label>
<input type="file" name="attachments[]" 
       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
       multiple>

<?php
$attQ = mysqli_query($conn,"SELECT * FROM incident_attachments WHERE incident_id=$id");

if(mysqli_num_rows($attQ) > 0){
    echo "<label style='margin-top:15px;'>Existing Attachments</label>";
    echo "<div class='attachment-list'>";
    
    while($att = mysqli_fetch_assoc($attQ)){
        echo "
        <div class='file-badge'>
            <a href='{$att['file_path']}' target='_blank'>
                📎 ".basename($att['file_path'])."
            </a>
            <a href='delete_attachment.php?id={$att['id']}&incident_id=$id' 
               class='delete-attach'
               onclick=\"return confirm('Delete this attachment?')\">
               ✖
            </a>
        </div>";
    }

    echo "</div>";
}
?>

<label>Unit where Incident occurred</label>
<input name="unit" value="<?= $data['unit'] ?>">

<label>Section of the Unit where Incident occurred</label>
<input name="section" value="<?= $data['section'] ?>">

<label>Brief Description of the Incident</label>
<textarea name="description"><?= $data['description'] ?></textarea>

<label>People Involved</label>
<textarea name="people_involved"><?= $data['people_involved'] ?></textarea>

<label>Area Operator</label>
<textarea name="area_operator"><?= $data['area_operator'] ?></textarea>

<label>Shift Incharge</label>
<textarea name="shift_incharge"><?= $data['shift_incharge'] ?></textarea>

<label>Maintenance Technician / Engineer</label>
<textarea name="maintenance_technician"><?= $data['maintenance_technician'] ?></textarea>

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

/* LOAD EXISTING ROWS FROM PHP */
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
    rows.push({
        id:"",
        recommendation:"",
        resp:"",
        target_date:"",
        remarks:"",
        status:"pending"
    });
    renderTable();
}

function deleteRow(index){
    rows.splice(index,1);
    renderTable();
}

function updateField(index, field, value){
    rows[index][field] = value;
}

function renderTable(){
    const tbody = document.getElementById("tableBody");
    tbody.innerHTML = "";

    const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
    if(currentPage > totalPages) currentPage = totalPages;
    if(currentPage < 1) currentPage = 1;

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.slice(start, end).forEach((row, i) => {
        const realIndex = start + i;

        tbody.innerHTML += `
        <tr>
            <td>${realIndex + 1}</td>

            <td>
                <input type="hidden" name="rec_id[]" value="${row.id}">
                <textarea name="recommendation[]" 
                    oninput="updateField(${realIndex}, 'recommendation', this.value)">
                    ${row.recommendation || ""}
                </textarea>
            </td>

            <td>
                <input type="text" name="resp[]" 
                    value="${row.resp || ""}"
                    oninput="updateField(${realIndex}, 'resp', this.value)">
            </td>

            <td>
                <input type="date" name="target_date[]" 
                    value="${row.target_date || ""}"
                    onchange="updateField(${realIndex}, 'target_date', this.value)">
            </td>

            <td>
                <textarea name="remarks[]" 
                    oninput="updateField(${realIndex}, 'remarks', this.value)">
                    ${row.remarks || ""}
                </textarea>
            </td>

            <td>
                <select name="status[]" 
                    onchange="updateField(${realIndex}, 'status', this.value)">
                    <option value="pending" ${row.status=="pending"?"selected":""}>Pending</option>
                    <option value="approved" ${row.status=="approved"?"selected":""}>Approved</option>
                </select>
            </td>

            <td>
                <button type="button" class="delete-btn" 
                    onclick="deleteRow(${realIndex})">✖</button>
            </td>
        </tr>`;
    });

    document.getElementById("pageInfo").innerText =
        "Page " + currentPage + " of " + totalPages;
}

function nextPage(){
    if(currentPage < Math.ceil(rows.length / rowsPerPage)){
        currentPage++;
        renderTable();
    }
}

function prevPage(){
    if(currentPage > 1){
        currentPage--;
        renderTable();
    }
}

renderTable();
</script>

</body>
</html>