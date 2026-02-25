<?php
include "db.php";

$id = intval($_GET['id']);
$q = mysqli_query($conn,"SELECT * FROM incident_attachments WHERE incident_id=$id");

while($row = mysqli_fetch_assoc($q)){
    echo "<div class='file-item'>
            <a href='{$row['file_path']}' target='_blank'>
            📎 ".basename($row['file_path'])."
            </a>
          </div>";
}
?>