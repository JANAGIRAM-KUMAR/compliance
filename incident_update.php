<?php
session_start();
include "db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

$id = intval($_POST['id']);

/* ===============================
   CHECK IF REPORT IS APPROVED
================================= */
$check = mysqli_query($conn, "SELECT status FROM incident_reports WHERE id=$id");
$row = mysqli_fetch_assoc($check);

if (!$row) {
    die("Report not found");
}

if ($row['status'] === 'approved') {
    die("Approved reports cannot be edited");
}

/* ===============================
   START TRANSACTION
================================= */
mysqli_begin_transaction($conn);

try {

    /* ===============================
       UPDATE MAIN INCIDENT TABLE
    ================================= */
    $sql = "UPDATE incident_reports SET
        iir_no='" . mysqli_real_escape_string($conn, $_POST['iir_no']) . "',
        incident_date='" . mysqli_real_escape_string($conn, $_POST['incident_date']) . "',
        unit='" . mysqli_real_escape_string($conn, $_POST['unit']) . "',
        section='" . mysqli_real_escape_string($conn, $_POST['section']) . "',
        description='" . mysqli_real_escape_string($conn, $_POST['description']) . "',
        people_involved='" . mysqli_real_escape_string($conn, $_POST['people_involved']) . "',
        injured_condition='" . mysqli_real_escape_string($conn, $_POST['injured_condition']) . "',
        root_cause='" . mysqli_real_escape_string($conn, $_POST['root_cause']) . "'
        WHERE id=$id";

    mysqli_query($conn, $sql);

    /* ===============================
       HANDLE ATTACHMENT (Separate Table)
    ================================= */
    if (!empty($_FILES['attachment']['name'])) {

        $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
        $ext = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid file type");
        }

        $uploadDir = "uploads/incident_reports/";
        $fileName = time() . "_" . basename($_FILES['attachment']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetPath)) {

            // delete old attachments
            mysqli_query($conn, "DELETE FROM attachments WHERE incident_id=$id");

            // insert new one
            mysqli_query($conn, "INSERT INTO attachments (incident_id, file_path)
                                 VALUES ($id, '$targetPath')");
        }
    }

    /* ===============================
       HANDLE RECOMMENDATIONS
    ================================= */

    // Get existing DB recommendation IDs
    $existingIds = [];
    $res = mysqli_query($conn, "SELECT id FROM incident_recommendations WHERE incident_id=$id");
    while ($r = mysqli_fetch_assoc($res)) {
        $existingIds[] = $r['id'];
    }

    $submittedIds = [];

    if (!empty($_POST['recommendation'])) {

        foreach ($_POST['recommendation'] as $key => $value) {

            $recId = $_POST['rec_id'][$key];
            $rec = mysqli_real_escape_string($conn, $_POST['recommendation'][$key]);
            $resp = mysqli_real_escape_string($conn, $_POST['resp'][$key]);
            $date = mysqli_real_escape_string($conn, $_POST['target_date'][$key]);
            $remarks = mysqli_real_escape_string($conn, $_POST['remarks'][$key]);
            $status = mysqli_real_escape_string($conn, $_POST['status'][$key]);

            if (!empty($recId)) {
                // UPDATE existing row
                mysqli_query($conn, "UPDATE incident_recommendations SET
                    recommendation='$rec',
                    resp='$resp',
                    target_date='$date',
                    remarks='$remarks',
                    status='$status'
                    WHERE id=$recId");

                $submittedIds[] = $recId;
            } else {
                // INSERT new row
                mysqli_query($conn, "INSERT INTO incident_recommendations
                    (incident_id, recommendation, resp, target_date, remarks, status)
                    VALUES
                    ($id, '$rec', '$resp', '$date', '$remarks', '$status')");

                $submittedIds[] = mysqli_insert_id($conn);
            }
        }
    }

    /* ===============================
       DELETE REMOVED ROWS
    ================================= */
    foreach ($existingIds as $dbId) {
        if (!in_array($dbId, $submittedIds)) {
            mysqli_query($conn, "DELETE FROM incident_recommendations WHERE id=$dbId");
        }
    }

    /* ===============================
       COMMIT TRANSACTION
    ================================= */
    mysqli_commit($conn);

} catch (Exception $e) {

    mysqli_rollback($conn);
    die("Error: " . $e->getMessage());
}

header("Location: incident_view.php?id=$id");
exit;
?>