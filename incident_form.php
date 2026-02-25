<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
include "db.php";

// Get next ID
$result = mysqli_query($conn, "SELECT AUTO_INCREMENT 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_NAME = 'incident_reports'");

$row = mysqli_fetch_assoc($result);
$next_id = $row['AUTO_INCREMENT'] ?? 1;

// Format: IIR/2026/001
$year = date("Y");
$formatted_iir = "IIR/" . $year . "/" . str_pad($next_id, 3, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Incident Report</title>

    <style>
        body {
            background: #f4f7f6;
            font-family: "Segoe UI";
        }

        .form-box {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 35px;
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .1);
        }

        h2 {
            margin-top: 0;
            color: #1b8f4c;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 18px;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        textarea {
            min-height: 90px;
        }

        button {
            margin-top: 25px;
            background: #1b8f4c;
            color: #fff;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

         .form-header {
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e5e7eb;
}

.header-left {
    justify-self: start;
}

.header-right {
    justify-self: end;
}

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
    border: 1px solid #d1dbd2;
    transition: all 0.25s ease;
}

.back-btn .arrow {
    font-size: 16px;
    transition: transform 0.25s ease;
}

.back-btn:hover {
    background: #1b8f4c;
    color: white;
    border-color: #1b8f4c;
    box-shadow: 0 4px 12px rgba(27, 143, 76, 0.25);
}

.back-btn:hover .arrow {
    transform: translateX(-4px);
}

        .tpl-logo {
            width: 42px;
            height: auto;
            cursor: pointer;
        }

        .recommendation-wrapper {
            margin-top: 20px;
            overflow-x: auto;
        }

        .recommendation-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        }

        .recommendation-table th {
            background: #1b8f4c;
            color: #fff;
            padding: 12px;
            font-size: 14px;
            text-align: center;
        }

        .recommendation-table td {
            border: 1px solid #e0e0e0;
            padding: 8px;
            vertical-align: top;
        }

        /* Center S.No column */
        .recommendation-table td:first-child,
        .recommendation-table th:first-child {
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
        }

        /* Center Action column */
        .recommendation-table td:last-child,
        .recommendation-table th:last-child {
            text-align: center;
            vertical-align: middle;
        }

        /* Professional Input Styling */

        .recommendation-table td {
            background: #fafafa;
        }

        .recommendation-table textarea,
        .recommendation-table input[type="text"],
        .recommendation-table input[type="date"],
        .recommendation-table select {

            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            font-size: 14px;
            font-family: "Segoe UI";
            transition: all 0.25s ease;
            box-sizing: border-box;
        }

        /* Bigger textarea */
        .recommendation-table textarea {
            min-height: 90px;
            resize: vertical;
            line-height: 1.5;
        }

        /* Clean focus effect */
        .recommendation-table textarea:focus,
        .recommendation-table input:focus,
        .recommendation-table select:focus {
            border-color: #1b8f4c;
            box-shadow: 0 0 0 3px rgba(27, 143, 76, 0.15);
            outline: none;
        }

        /* Better date box */
        .recommendation-table input[type="date"] {
            padding: 10px 12px;
        }

        /* Status dropdown styling */
        .recommendation-table select {
            font-weight: 600;
        }

        /* Row hover effect */
        .recommendation-table tbody tr:hover {
            background: #f3fdf7;
        }

        .recommendation-table textarea:focus,
        .recommendation-table input:focus,
        .recommendation-table select:focus {
            border-color: #1b8f4c;
            outline: none;
            box-shadow: 0 0 4px rgba(27, 143, 76, .3);
        }

        .recommendation-table textarea {
            min-height: 70px;
            resize: vertical;
        }

        .status-pending {
            background: #fff4e5;
            color: #d97706;
            font-weight: 600;
        }

        .status-approved {
            background: #e6f9ef;
            color: #15803d;
            font-weight: 600;
        }

        .add-row-btn {
            margin-top: 12px;
            background: #1b8f4c;
            color: #fff;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: .2s;
        }

        .add-row-btn:hover {
            background: #157a3f;
        }

        .delete-btn {
            background: #ef4444;
            border: none;
            color: white;
            padding: 6px 8px;
            border-radius: 20px;
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .pagination-bar {
            margin-top: 14px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .page-btn {
            background: #1b8f4c;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: .2s;
        }

        .page-btn:hover {
            background: #157a3f;
        }
    </style>
</head>

<body>


    <div class="form-box">
        <form method="POST" action="incident_save.php" enctype="multipart/form-data">
            <div class="form-header">

    <div class="header-left">
        <a href="incident_investigation.php" class="back-btn">
        <span class="arrow">←</span>
        Back
    </a>
    </div>

    <h2>Incident Investigation Report</h2>

    <div class="header-right">
        <a href="incident_investigation.php">
            <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
        </a>
    </div>

</div>

            <label>IIR Number</label>
            <input value="<?php echo $formatted_iir; ?>" readonly style="background:#f3f4f6; font-weight:600;">
            <input type="hidden" name="iir_no" value="<?php echo $formatted_iir; ?>">

            <label>Date & Time of Incident</label>
            <input type="datetime-local" name="incident_date" required>

            <label>Attach Investigation File (PDF / Image / DOC)</label>
            <input type="file" name="attachments[]" 
       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" 
       multiple>

            <label>Unit where Incident occurred</label>
            <input name="unit">

            <label>Section of the Unit where Incident occurred</label>
            <input name="section">

            <label>Brief Description of the Incident</label>
            <textarea name="description"></textarea>

            <label>People Involved</label>
            <textarea name="people_involved"></textarea>

            <label>Area Operator</label>
            <textarea name="area_operator"></textarea>

            <label>Shift Incharge</label>
            <textarea name="shift_incharge"></textarea>

            <label>Maintenance Technician / Engineer</label>
            <textarea name="maintenance_technician"></textarea>

            <label>Brief Description of the Condition of Injured & Action Taken</label>
            <textarea name="injured_condition"></textarea>

            <label>Root Cause of the Incident</label>
            <textarea name="root_cause"></textarea>

            <label style="margin-top:30px;font-weight:700;">
                Recommendation and action plan to avoid in future with agreed timeline:
            </label>

            <div class="recommendation-wrapper">

                <table class="recommendation-table">
                    <thead>
                        <tr>
                            <th style="width:60px;">S.No</th>
                            <th>Recommendation & Observations</th>
                            <th style="width:120px;">Resp</th>
                            <th style="width:140px;">Target Date</th>
                            <th>Remarks</th>
                            <th style="width:120px;">Status</th>
                            <th style="width:70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>

                <div class="pagination-bar">
                    <button type="button" class="page-btn" onclick="prevPage()">❮</button>
                    <span id="pageInfo"></span>
                    <button type="button" class="page-btn" onclick="nextPage()">❯</button>
                </div>

                <button type="button" class="add-row-btn" onclick="addRow()">+ Add Row</button>

            </div>

            <button type="submit">Save Report</button>

        </form>
    </div>
</body>
<script>
    let rows = [];
    let currentPage = 1;
    const rowsPerPage = 10;
    const userRole = "<?php echo $_SESSION['role']; ?>";

    function addRow() {
        rows.push({
            recommendation: "",
            resp: "",
            target_date: "",
            remarks: "",
            status: "Pending"
        });
        renderTable();
    }

    function deleteRow(index) {
        rows.splice(index, 1);
        renderTable();
    }

    function updateField(index, field, value) {
        rows[index][field] = value;
    }

    function renderTable() {
        const tbody = document.getElementById("tableBody");
        tbody.innerHTML = "";

        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageRows = rows.slice(start, end);

        pageRows.forEach((row, i) => {
            const realIndex = start + i;

            tbody.innerHTML += `
        <tr>
            <td>${realIndex + 1}</td>

            <td>
                <textarea name="recommendation[]" 
                oninput="updateField(${realIndex}, 'recommendation', this.value)">
                ${row.recommendation || ""}
                </textarea>
            </td>

            <td>
                <input type="text" name="resp[]" 
                value="${row.resp || ""}"
                oninput="updateField(${realIndex}, 'resp', this.value)">
            </td>

            <td>
                <input type="date" name="target_date[]" 
                value="${row.target_date || ""}"
                onchange="updateField(${realIndex}, 'target_date', this.value)">
            </td>

            <td>
                <textarea name="remarks[]" 
                oninput="updateField(${realIndex}, 'remarks', this.value)">
                ${row.remarks || ""}
                </textarea>
            </td>

            <td>
                ${userRole === "admin"
                    ? `<select name="status[]" 
                        onchange="updateField(${realIndex}, 'status', this.value)">
                        <option value="Pending" ${row.status === "Pending" ? "selected" : ""}>Pending</option>
                        <option value="Approved" ${row.status === "Approved" ? "selected" : ""}>Approved</option>
                       </select>`
                    : `<input type="text" name="status[]" 
                        value="${row.status}" readonly>`
                }
            </td>

            <td>
                <button type="button" class="delete-btn" 
                onclick="deleteRow(${realIndex})">✖</button>
            </td>
        </tr>
        `;
        });

        document.getElementById("pageInfo").innerText =
            "Page " + currentPage + " of " + totalPages;
    }

    function nextPage() {
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
            currentPage++;
            renderTable();
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    }

    addRow();
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

</html>