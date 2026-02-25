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
        .header {
    margin-bottom: 30px;
}

/* Top row */
.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

/* Bottom row */
.bottom-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-actions {
    display: flex;
    gap: 12px;
}

/* Back Button */
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
    transition: all 0.25s ease;
}

.back-btn:hover {
    background: #1b8f4c;
    color: white;
}

/* Buttons */
.add-btn {
    background: #1b8f4c;
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}

.add-btn.secondary {
    background: #e5e7eb;
    color: #374151;
}

/* Logo */
.tpl-logo {
    width: 42px;
}
    </style>
</head>

<body>
    <div class="container">

        <div class="header">

            <div class="top-bar">
                <a href="dashboard.php" class="back-btn">
                    <span class="arrow">←</span> Back
                </a>

                <a href="dashboard.php">
                    <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
                </a>
            </div>

            <div class="bottom-bar">
                <h2>Incident Investigation Reports</h2>

                
                    <div class="header-actions">
                        <a href="incident_summary.php" class="add-btn secondary">
                            📄 Reports
                        </a>
                        <?php if ($role === 'admin'): ?>
                        <a href="incident_form.php" class="add-btn">
                            + New Report
                        </a>
                         <?php endif; ?>
                    </div>
               
            </div>

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