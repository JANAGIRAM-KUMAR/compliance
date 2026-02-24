<?php
session_start();
include "db.php";

$status = $_GET['status'] ?? 'pending';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=incident_$status.xls");

echo "IIR No\tRecommendation\tResp\tTarget Date\tRemarks\tStatus\n";

$sql = "
SELECT ir.iir_no, r.*
FROM incident_reports ir
JOIN incident_recommendations r ON ir.id = r.incident_id
WHERE r.status='$status'
";

$result = mysqli_query($conn, $sql);

while($row = mysqli_fetch_assoc($result)) {
    echo $row['iir_no']."\t".
         $row['recommendation']."\t".
         $row['resp']."\t".
         $row['target_date']."\t".
         $row['remarks']."\t".
         $row['status']."\n";
}
?>