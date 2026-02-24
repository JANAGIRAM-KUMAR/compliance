<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
include "db.php";

$role = $_SESSION['role'];

$sql = ($role === 'admin')
    ? "SELECT * FROM incident_reports ORDER BY created_at DESC"
    : "SELECT * FROM incident_reports WHERE status='pending' ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Incident Investigation</title>
    <style>
        body {
            background: #f4f7f6;
            font-family: "Segoe UI";
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 40px;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-left h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }

        .tpl-logo {
            width: 42px;
            height: auto;
            object-fit: contain;
        }

        .add-btn {
            background: #1b8f4c;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 22px;
        }

        .card {
            background: #fff;
            padding: 22px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
            border-left: 6px solid #1b8f4c;
            text-decoration: none;
            color: #1f2937;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .pending {
            background: #fff3cd;
        }

        .approved {
            background: #d4edda;
        }

        .empty {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            color: #666;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        }
    </style>
</head>

<body>
    <div class="container">

        <div class="header">
            <div class="header-left">
                <a href="dashboard.php" title="Back to Dashboard">
                    <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
                </a>
                <h2>Incident Investigation Reports</h2>
            </div>

            <?php if ($role === 'admin'): ?>
                <div style="display:flex; gap:10px;">
                    <a href="incident_summary.php" class="add-btn">
                        📄 Reports
                    </a>
                    <a href="incident_form.php" class="add-btn">
                        + New Report
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($count === 0): ?>
            <div class="empty">
                <?php if ($role === 'admin'): ?>
                    No incident reports created yet.
                <?php else: ?>
                    No pending incident reports available at the moment.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <a class="card" href="incident_view.php?id=<?= $row['id'] ?>">
                        <h3><?= $row['iir_no'] ?></h3>
                        <p><b>Unit:</b> <?= $row['unit'] ?></p>
                        <p><b>Date:</b> <?= date('d M Y, H:i', strtotime($row['incident_date'])) ?></p>
                        <span class="badge <?= $row['status'] ?>">
                            <?= strtoupper($row['status']) ?>
                        </span>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>