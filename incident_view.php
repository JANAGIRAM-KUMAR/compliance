<?php
session_start();
include "db.php";

$id = $_GET['id'];
$role = $_SESSION['role'];

$q = mysqli_query($conn, "SELECT * FROM incident_reports WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if ($role !== 'admin' && $data['status'] === 'approved') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Incident Report</title>
    <style>
        body {
            background: #f4f7f6;
            font-family: "Segoe UI";
        }

        .report {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .1);
            position: relative;
            /* IMPORTANT */
        }

        /* HEADER INSIDE CARD */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .tpl-logo {
            width: 42px;
            height: auto;
        }

        /* ACTION BUTTONS */
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 12px;
        }


        .edit {
            background: #1976d2;
            color: #fff;
        }

        .approve {
            background: #1b8f4c;
            color: #fff;
        }

        .readonly {
            background: #e0e0e0;
            color: #555;
            padding: 12px;
            border-radius: 8px;
            margin-top: 20px;
        }

        /* BASE BUTTON */
        .btn {
            min-width: 140px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            gap: 8px;
        }

        /* FILE ACTIONS */
        .btn-view {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .btn-download {
            background: #e0f2f1;
            color: #00695c;
        }

        /* WORKFLOW ACTIONS */
        .btn-edit {
            background: #1976d2;
            color: #fff;
        }

        .btn-approve {
            background: #1b8f4c;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="report">

        <!-- CARD HEADER -->
        <div class="card-header">
            <h2><?= $data['iir_no'] ?></h2>
            <a href="dashboard.php" title="Back to Dashboard">
                <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
            </a>
        </div>

        <p><b>Date & Time:</b> <?= $data['incident_date'] ?></p>
        <p><b>Unit:</b> <?= $data['unit'] ?></p>
        <p><b>Section:</b> <?= $data['section'] ?></p>

        <p><b>Description:</b><br><?= nl2br($data['description']) ?></p>
        <p><b>People Involved:</b><br><?= nl2br($data['people_involved']) ?></p>
        <p><b>Injury & Action:</b><br><?= nl2br($data['injured_condition']) ?></p>
        <p><b>Root Cause:</b><br><?= nl2br($data['root_cause']) ?></p>
        <p><b>Recommendations:</b><br><?= nl2br($data['recommendations']) ?></p>

        <?php if (!empty($data['attachment'])): ?>
            <div style="margin-top:25px;">
                <b>Attachment:</b>

                <div class="actions" style="margin-top:12px;">
                    <a href="<?= $data['attachment'] ?>" target="_blank" class="btn btn-view">
                        View File
                    </a>

                    <a href="download.php?id=<?= $id ?>" class="btn btn-download">
                        Download File
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($role === 'admin' && $data['status'] === 'pending'): ?>
            <div class="actions">
                <a href="incident_edit.php?id=<?= $id ?>" class="btn btn-edit">
                    Edit report
                </a>

                <form method="post" action="incident_approve.php">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-approve">
                        Approve
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="readonly">
                This report is read-only.
            </div>
        <?php endif; ?>


    </div>

</body>

</html>