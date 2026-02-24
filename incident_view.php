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
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .08);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 15px;
        }

        .card-header h2 {
            color: #1b8f4c;
            margin: 0;
        }

        .tpl-logo {
            width: 42px;
        }

        .report p {
            margin-bottom: 14px;
            line-height: 1.6;
        }

        .report b {
            color: #333;
        }

        h3 {
            margin-top: 35px;
            color: #1b8f4c;
        }

        /* Recommendations Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .05);
        }

        table th {
            background: #1b8f4c;
            color: white;
            padding: 12px;
            text-align: center;
            font-size: 14px;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        table tr:hover {
            background: #f3fdf7;
        }

        /* Status Styling */
        .status-approved {
            background: #e6f9ef;
            color: #15803d;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff4e5;
            color: #d97706;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Attachments */
        .attachment-section {
            margin-top: 35px;
        }

        .file-card {
            background: #f9fafb;
            padding: 15px 18px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            border: 1px solid #e5e7eb;
        }

        .file-info {
            font-weight: 600;
            color: #374151;
        }

        .file-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            min-width: 100px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-view {
            background: #e3f2fd;
            color: #0d47a1;
        }

        .btn-download {
            background: #e0f2f1;
            color: #00695c;
        }

        .btn-edit {
            background: #1976d2;
            color: white;
        }

        .btn-approve {
            background: #1b8f4c;
            color: white;
        }

        .actions {
            margin-top: 35px;
            display: flex;
            gap: 15px;
        }

        .readonly {
            margin-top: 30px;
            background: #f1f5f9;
            padding: 14px;
            border-radius: 10px;
            color: #555;
            text-align: center;
            font-weight: 500;
        }

        /* Status Column */
        .status-cell {
            text-align: center;
            vertical-align: middle;
        }

        /* Badge Style */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.approved {
            background: #e6f9ef;
            color: #15803d;
        }

        .status-badge.pending {
            background: #fff4e5;
            color: #d97706;
        }

        /* Align badge + button perfectly */
        .status-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* Remove form spacing */
        .inline-form {
            margin: 0;
        }

        /* Better Approve Button */
        .btn-approve-small {
            background: #1b8f4c;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn-approve-small:hover {
            background: #157a3f;
            transform: scale(1.05);
        }
        
    </style>
</head>

<body>

    <div class="report">

        <!-- CARD HEADER -->
        <div class="card-header">
            <h2>IIR NO: <?= $data['iir_no'] ?></h2>
            <a href="dashboard.php" title="Back to Dashboard">
                <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
            </a>
        </div>

        <p><b>Date & Time:</b> <?= $data['incident_date'] ?></p>
        <p><b>Unit:</b> <?= $data['unit'] ?></p>
        <p><b>Section:</b> <?= $data['section'] ?></p>

        <p><b>Description:</b><br><?= nl2br($data['description']) ?></p>
        <p><b>People Involved:</b><br><?= nl2br($data['people_involved']) ?></p>
        <p><b>Area Operator:</b><br></p>
        <p><b>Shift Incharge:</b><br></p>
        <p><b>Maintenance Technician / Engineer:</b><br></p>
        <p><b>Brief Description of the Condition of Injured & Action Taken:</b><br><?= nl2br($data['injured_condition']) ?></p>
        <p><b>Root Cause of the Incident:</b><br><?= nl2br($data['root_cause']) ?></p>

        <?php
        $files = mysqli_query($conn, "SELECT * FROM incident_attachments WHERE incident_id=$id");
        if (mysqli_num_rows($files) > 0):
            ?>
            <div class="attachment-section">
                <h3>Attachments</h3>

                <?php while ($file = mysqli_fetch_assoc($files)): ?>
                    <div class="file-card">
                        <div class="file-info">
                            📄 <?= basename($file['file_path']) ?>
                        </div>

                        <div class="file-actions">
                            <a href="<?= $file['file_path'] ?>" target="_blank" class="btn btn-view">
                                View
                            </a>

                            <!-- <a href="<?= $file['file_path'] ?>" download class="btn btn-download">
                                Download
                            </a> -->
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>
        <?php endif; ?>

        <h3 style="margin-top:25px;">Recommendations & Action Plan</h3>

        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
            <tr style="background:#1b8f4c; color:white;">
                <th style="padding:8px;">S.No</th>
                <th>Recommendation</th>
                <th>Resp</th>
                <th>Target Date</th>
                <th>Remarks</th>
                <th>Status</th>
            </tr>

            <?php
            $rq = mysqli_query($conn, "SELECT * FROM incident_recommendations WHERE incident_id=$id");

            $i = 1;
            while ($row = mysqli_fetch_assoc($rq)):
                ?>

                <tr style="border-bottom:1px solid #ddd;">
                    <td style="text-align:center;"><?= $i++ ?></td>
                    <td><?= nl2br($row['recommendation']) ?></td>
                    <td><?= $row['resp'] ?></td>
                    <td><?= $row['target_date'] ?></td>
                    <td><?= nl2br($row['remarks']) ?></td>
                    <td class="status-cell">
                        <?php if ($row['status'] == 'approved'): ?>
                            <span class="status-badge approved">✔ Approved</span>
                        <?php else: ?>
                            <div class="status-wrapper">
                                <span class="status-badge pending">Pending</span>

                                <?php if ($role === 'admin'): ?>
                                    <form method="post" action="approve_incident.php" class="inline-form">
                                        <input type="hidden" name="rec_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="incident_id" value="<?= $id ?>">
                                        <button type="submit" class="btn-approve-small">
                                            Approve
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endwhile; ?>

        </table>



        <?php if ($role === 'admin' && $data['status'] === 'pending'): ?>
            <div class="actions">
                <a href="incident_edit.php?id=<?= $id ?>" class="btn btn-edit">
                    Edit report
                </a>
            </div>
        <?php else: ?>
            <div class="readonly">
                This report is read-only.
            </div>
        <?php endif; ?>
    </div>
</body>

</html>