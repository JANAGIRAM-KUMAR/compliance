<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TPL Compliance Dashboard</title>

    <style>
        :root {
            --primary: #1b8f4c;
            --primary-dark: #166f3c;
            --bg: #f4f7f6;
            --card-bg: #ffffff;
            --text-dark: #1f2937;
            --text-muted: #6b7280;
            --shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", system-ui, sans-serif;
            background: var(--bg);
            color: var(--text-dark);
        }

        /* NAVBAR */
        .navbar {
            background: white;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .nav-left img {
            width: 42px;
        }

        .nav-left h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .logout-btn {
            background: var(--primary);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .logout-btn:hover {
            background: var(--primary-dark);
        }

        /* PAGE CONTENT */
        .content {
            padding: 40px 32px;
            max-width: 1200px;
            margin: auto;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .page-subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        /* CARD */
        .card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 14px;
            box-shadow: var(--shadow);
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-left: 6px solid var(--primary);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px rgba(0,0,0,0.12);
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 20px;
            color: var(--primary);
        }

        .card p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* FOOTER SPACE */
        .footer-space {
            height: 40px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="nav-left">
            <img src="images/tpl.png" alt="TPL Logo">
            <h3>TPL Compliance Dashboard</h3>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- CONTENT -->
    <div class="content">
        <div class="page-title">Compliance Modules</div>
        <div class="page-subtitle">
            Access all safety, audit, and compliance related activities from one place
        </div>

        <div class="grid">
            <a href="incident_investigation.php" class="card">
                <h2>Incident Investigation</h2>
                <p>Report, review, and manage workplace incident investigations.</p>
            </a>

            <a href="mock_drill.php" class="card">
                <h2>Mock Drill</h2>
                <p>Plan, schedule, and evaluate emergency mock drills.</p>
            </a>

            <a href="iso_documents.php" class="card">
                <h2>ISO Documents</h2>
                <p>View and manage ISO standards, policies, and procedures.</p>
            </a>

            <a href="safety_training.php" class="card">
                <h2>Safety Training</h2>
                <p>Employee safety training programs and attendance records.</p>
            </a>

            <a href="audit_reports.php" class="card">
                <h2>Audit Reports</h2>
                <p>Internal and external audit findings and compliance status.</p>
            </a>

            <a href="compliance_checklist.php" class="card">
                <h2>Compliance Checklist</h2>
                <p>Track statutory and internal compliance requirements.</p>
            </a>
        </div>
    </div>

    <div class="footer-space"></div>

</body>
</html>
