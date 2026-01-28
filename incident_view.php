<?php
session_start();
include "db.php";

function parseRecommendations($text) {
    $rows = [];
    $blocks = explode('--------------------------', $text);

    foreach ($blocks as $block) {
        if (trim($block) === '') continue;

        preg_match('/Recommendation:\s*(.*)/', $block, $rec);
        preg_match('/Responsible:\s*(.*)/', $block, $resp);
        preg_match('/Target Date:\s*(.*)/', $block, $date);
        preg_match('/Remarks:\s*(.*)/', $block, $remarks);

        $rows[] = [
            'text' => $rec[1] ?? '',
            'resp' => $resp[1] ?? '',
            'date' => $date[1] ?? '',
            'remarks' => $remarks[1] ?? ''
        ];
    }

    return $rows;
}

$recRows = parseRecommendations($data['recommendations'] ?? '');


$id   = intval($_GET['id']);
$role = $_SESSION['role'] ?? '';

/* ================= FETCH INCIDENT ================= */
$q = mysqli_query($conn, "SELECT * FROM incident_reports WHERE id=$id");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Incident not found");
}

if ($role !== 'admin' && $data['status'] === 'approved') {
    die("Access denied");
}

/* ================= FETCH ATTACHMENTS ================= */
$attachments = [];
$aq = mysqli_query($conn, "
    SELECT * FROM incident_attachments 
    WHERE incident_id = $id
");
while ($row = mysqli_fetch_assoc($aq)) {
    $attachments[] = $row;
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
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .tpl-logo {
            width: 42px;
        }

        .actions {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

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
            color: #fff;
        }

        .btn-approve {
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

        .file-box {
            background: #fafafa;
            padding: 12px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .file-name {
            font-weight: 600;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>

<div class="report">

    <!-- HEADER -->
    <div class="card-header">
        <h2><?= htmlspecialchars($data['iir_no']) ?></h2>
        <a href="dashboard.php">
            <img src="images/tpl.png" class="tpl-logo">
        </a>
    </div>

    <p><b>Date & Time:</b> <?= $data['incident_date'] ?></p>
    <p><b>Unit:</b> <?= htmlspecialchars($data['unit']) ?></p>
    <p><b>Section:</b> <?= htmlspecialchars($data['section']) ?></p>

    <p><b>Description:</b><br><?= nl2br(htmlspecialchars($data['description'])) ?></p>
    <p><b>People Involved:</b><br><?= nl2br(htmlspecialchars($data['people_involved'])) ?></p>
    <p><b>Area Operator:</b> <?= htmlspecialchars($data['area_operator']) ?></p>
    <p><b>Shift Incharge:</b> <?= htmlspecialchars($data['shift_incharge']) ?></p>
    <p><b>Maintenance Technician / Engineer:</b> <?= htmlspecialchars($data['maintenance_technician']) ?></p>

    <p><b>Injury & Action:</b><br><?= nl2br(htmlspecialchars($data['injured_condition'])) ?></p>
    <p><b>Root Cause:</b><br><?= nl2br(htmlspecialchars($data['root_cause'])) ?></p>
    <?php if (!empty($recRows)): ?>
    <h3>Recommendation and Action Plan</h3>
    <table style="width:100%; border-collapse:collapse; margin-top:10px;">
        <tr style="background:#1b8f4c; color:#fff;">
            <th>S.No</th>
            <th>Recommendation</th>
            <th>Responsible</th>
            <th>Target Date</th>
            <th>Remarks</th>
        </tr>
        <?php foreach ($recRows as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['text']) ?></td>
            <td><?= htmlspecialchars($r['resp']) ?></td>
            <td><?= htmlspecialchars($r['date']) ?></td>
            <td><?= htmlspecialchars($r['remarks']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>


    <!-- ATTACHMENTS -->
    <?php if (!empty($attachments)): ?>
        <div style="margin-top:25px;">
            <b>Attachments:</b>

            <?php foreach ($attachments as $file): ?>
                <div class="file-box">
                    <div class="file-name">
                        <?= basename($file['file_path']) ?>
                    </div>

                    <div class="actions">
                        <a href="<?= $file['file_path'] ?>" target="_blank" class="btn btn-view">
                            View
                        </a>

                        <a href="download.php?file_id=<?= $file['id'] ?>" class="btn btn-download">
                            Download
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- WORKFLOW ACTIONS -->
    <?php if ($role === 'admin' && $data['status'] === 'pending'): ?>
        <div class="actions" style="margin-top:30px;">
            <a href="incident_edit.php?id=<?= $id ?>" class="btn btn-edit">
                Edit Report
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
