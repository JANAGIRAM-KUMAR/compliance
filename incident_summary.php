<?php
session_start();
include "db.php";

// if ($_SESSION['role'] !== 'admin') {
//     die("Access denied");
// }

$filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

if ($filter !== 'approved') {
    $filter = 'pending';
}

/* Get recommendations based on status */
$sql = "
SELECT ir.id as incident_id, ir.iir_no, r.*
FROM incident_reports ir
JOIN incident_recommendations r ON ir.id = r.incident_id
WHERE r.status='$filter'
ORDER BY ir.created_at DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports Summary</title>

<style>
body { background:#f4f7f6; font-family:"Segoe UI"; }

.container {
    max-width:1200px;
    margin:auto;
    padding:40px;
}

.header {
    margin-bottom: 30px;
}

/* Top bar */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

/* Title */
.page-title {
    margin: 10px 0 20px 0;
    font-size: 24px;
    font-weight: 700;
    color: #000000;
}

/* Action row */
.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

/* Groups */
.filter-group,
.download-group {
    display: flex;
    gap: 12px;
}

/* Back button */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 8px;
    background: #f3f4f6;
    color: #1b8f4c;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid #d1d5db;
    transition: 0.25s ease;
}

.back-btn:hover {
    background: #1b8f4c;
    color: white;
}

/* Filter buttons */
.filter-btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    background: #4e8f69;
    color: #374151;
    transition: 0.2s ease;
}

.filter-btn.active {
    background: green;
    color: white;
}

/* Download buttons */
.download-btn {
    background: #1b8f4c;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s ease;
}

.download-btn:hover {
    background: #157a3f;
}

.tpl-logo { width:42px; }

.filter-btn {
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    color:white;
}

.pending-btn { background:#d97706; }
.approved-btn { background:#15803d; }

.download-btn {
    background:#1b8f4c;
    color:white;
    padding:10px 16px;
    border-radius:8px;
    text-decoration:none;
    margin-left:10px;
}

table {
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 10px 25px rgba(0,0,0,.08);
    border-radius:12px;
    overflow:hidden;
}

th {
    background:#1b8f4c;
    color:white;
    padding:12px;
}

td {
    padding:10px;
    border-bottom:1px solid #eee;
}
.search-box {
    padding:8px 14px;
    border-radius:8px;
    border:1px solid #ccc;
    width:260px;
}

.view-btn {
    background:#1b8f4c;
    color:white;
    padding:6px 12px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}

.view-btn:hover {
    background:#157a3f;
}

/* MODAL */
.modal {
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.4);
    justify-content:center;
    align-items:center;
}

.modal-content {
    background:white;
    padding:25px;
    border-radius:12px;
    width:400px;
    max-height:400px;
    overflow:auto;
}

.close {
    float:right;
    cursor:pointer;
    font-size:18px;
}

.file-item {
    margin-top:8px;
}
</style>
</head>

<body>

<div class="container">

<div class="header">

    <!-- Top Row -->
    <div class="top-bar">
        <a href="incident_investigation.php" class="back-btn">
            <span class="arrow">←</span> Back
        </a>

        <a href="incident_investigation.php">
            <img src="images/tpl.png" class="tpl-logo">
        </a>
    </div>

    <!-- Title -->
    <h2 class="page-title">Reports Summary</h2>

    <!-- Actions Row -->
    <div class="action-bar">
        <div class="filter-group">
            <a href="?status=pending" 
               class="filter-btn <?= $filter=='pending'?'active':'' ?>">
               Pending
            </a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="?status=approved" 
               class="filter-btn <?= $filter=='approved'?'active':'' ?>">
               Approved
            </a>
            <?php endif; ?>
        </div>
        <input type="text" id="searchInput" 
           placeholder="Search by IIR No, Resp, Remarks..."
           class="search-box">


        <div class="download-group">
            <a href="export_excel.php?status=<?= $filter ?>" 
               class="download-btn">
               Download Excel
            </a>

            <a href="export_pdf.php?status=<?= $filter ?>" 
               class="download-btn">
               Download PDF
            </a>
        </div>
    </div>

</div>

<table>

<thead>
<tr>
    <th>IIR No</th>
    <th>Recommendation & Observations</th>
    <th>Resp</th>
    <th>Target Date</th>
    <th>Remarks</th>
    <th>Status</th>
    <th>Attachments</th>
</tr>
</thead>

<tbody id="tableBody">

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['iir_no'] ?></td>
    <td><?= $row['recommendation'] ?></td>
    <td><?= $row['resp'] ?></td>
    <td><?= $row['target_date'] ?></td>
    <td><?= $row['remarks'] ?></td>
    <td><?= strtoupper($row['status']) ?></td>

    <td>
    <?php
    $attCheck = mysqli_query($conn,
        "SELECT * FROM incident_attachments WHERE incident_id=".$row['incident_id']
    );

    if(mysqli_num_rows($attCheck) > 0){
    ?>
        <button class="view-btn"
            onclick="showAttachments(<?= $row['incident_id'] ?>)">
            View
        </button>
    <?php } else { echo "-"; } ?>
    </td>

</tr>
<?php endwhile; ?>

</tbody>

</table>
<div id="attachmentModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">×</span>
    <h3>Attachments</h3>
    <div id="attachmentList"></div>
  </div>
</div>
</div>
</body>
<script>
const rowsPerPage = 10;
let currentPage = 1;

function paginateTable() {
    const rows = document.querySelectorAll("#tableBody tr");
    rows.forEach((row, index) => {
        row.style.display = 
            (index >= (currentPage-1)*rowsPerPage && 
             index < currentPage*rowsPerPage)
             ? "" : "none";
    });
}

function nextPage(){
    const rows = document.querySelectorAll("#tableBody tr");
    if(currentPage*rowsPerPage < rows.length){
        currentPage++;
        paginateTable();
    }
}

function prevPage(){
    if(currentPage>1){
        currentPage--;
        paginateTable();
    }
}

paginateTable();

/* LIVE SEARCH */
document.getElementById("searchInput").addEventListener("keyup", function(){
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#tableBody tr");

    rows.forEach(row => {
        row.style.display = 
            row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
});

/* ATTACHMENT MODAL */
function showAttachments(id){
    fetch("fetch_attachments.php?id="+id)
    .then(res => res.text())
    .then(data => {
        document.getElementById("attachmentList").innerHTML = data;
        document.getElementById("attachmentModal").style.display="flex";
    });
}

function closeModal(){
    document.getElementById("attachmentModal").style.display="none";
}
</script>
</html>