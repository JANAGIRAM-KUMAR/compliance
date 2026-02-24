<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
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
                <h2>Incident Investigation Report</h2>
                <a href="incident_investigation.php" title="Back to Dashboard">
                    <img src="images/tpl.png" alt="TPL Logo" class="tpl-logo">
                </a>
            </div>
            <label>IIR Number</label>
            <input name="iir_no" required>

            <label>Date & Time of Incident</label>
            <input type="datetime-local" name="incident_date" required>

            <label>Attach Investigation File (PDF / Image / DOC)</label>
            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">

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
        rows.push({});
        renderTable();
    }

    function deleteRow(index) {
        rows.splice(index, 1);
        renderTable();
    }

    function renderTable() {
        const tbody = document.getElementById("tableBody");
        tbody.innerHTML = "";

        const totalPages = Math.ceil(rows.length / rowsPerPage) || 1;
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageRows = rows.slice(start, end);

        pageRows.forEach((_, i) => {
            const realIndex = start + i;

            tbody.innerHTML += `
            <tr style="animation:fadeIn .2s ease;">
                <td>${realIndex + 1}</td>

                <td><textarea name="recommendation[]"></textarea></td>

                <td><input type="text" name="resp[]"></td>

                <td><input type="date" name="target_date[]"></td>

                <td><textarea name="remarks[]"></textarea></td>

                <td>
                    ${userRole === "admin"
                    ? `<select name="status[]" onchange="updateStatusStyle(this)">
                                <option value="Pending" selected>Pending</option>
                                <option value="Approved">Approved</option>
                           </select>`
                    : `<input type="text" name="status[]" value="Pending" readonly class="status-pending">`
                }
                </td>

                <td>
                    <button type="button" class="delete-btn" onclick="deleteRow(${realIndex})">✖</button>
                </td>
            </tr>
        `;
        });

        document.getElementById("pageInfo").innerText =
            "Page " + currentPage + " of " + totalPages;
    }

    function updateStatusStyle(select) {
        if (select.value === "Approved") {
            select.classList.add("status-approved");
            select.classList.remove("status-pending");
        } else {
            select.classList.add("status-pending");
            select.classList.remove("status-approved");
        }
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