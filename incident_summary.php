<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

if ($filter !== 'approved') {
    $filter = 'pending';
}

/* Get recommendations based on status */
$sql = "
SELECT ir.iir_no, r.*
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
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
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
</style>
</head>

<body>

<div class="container">

<div class="header">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="incident_investigation.php">
            <img src="images/tpl.png" class="tpl-logo">
        </a>
        <h2>Reports Summary</h2>
    </div>

    <div>
        <a href="?status=pending" class="filter-btn pending-btn">Pending</a>
        <a href="?status=approved" class="filter-btn approved-btn">Approved</a>

        <a href="export_excel.php?status=<?= $filter ?>" class="download-btn">Download Excel</a>
        <a href="export_pdf.php?status=<?= $filter ?>" class="download-btn">Download PDF</a>
    </div>
</div>

<table>
<tr>
    <th>IIR No</th>
    <th>Recommendation & Observations</th>
    <th>Resp</th>
    <th>Target Date</th>
    <th>Remarks</th>
    <th>Status</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['iir_no'] ?></td>
    <td><?= $row['recommendation'] ?></td>
    <td><?= $row['resp'] ?></td>
    <td><?= $row['target_date'] ?></td>
    <td><?= $row['remarks'] ?></td>
    <td><?= strtoupper($row['status']) ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</body>
</html>