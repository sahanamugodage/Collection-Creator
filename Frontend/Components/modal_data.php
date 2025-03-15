<?php
session_start();
include "../Connection/conn.php"; 


$dbName = isset($_SESSION['selected_db']) ? $_SESSION['selected_db'] : '';
if ($dbName) {
    $conn->select_db($dbName);
}


$tableName = isset($_GET['task_id']) ? $_GET['task_id'] : '';
if (!$tableName) {
    echo "Table name not provided.";
    exit;
}


$columnsQuery = "SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = '$dbName' AND table_name = '$tableName'";
$columnsResult = $conn->query($columnsQuery);

if ($columnsResult && $columnsResult->num_rows > 0) {
    $dataQuery = "SELECT * FROM `$tableName`";
    $dataResult = $conn->query($dataQuery);
    ?>

<div class="modal">
    <div class="modal-remove-icon">
        <i class="fas fa-times" onclick="closeModal()"></i>
    </div>
    <div class="modal-header">
        <h1 class="skeleton"><?php echo htmlspecialchars($tableName); ?></h1>
    </div>
    <div class="modal-body">
        <table class="table shadow radius-sm">
            <thead class="success-color radius-sm">
                <tr class="radius-sm">
                    <?php
                        while ($column = $columnsResult->fetch_assoc()) {
                            echo '<th>' . htmlspecialchars($column['COLUMN_NAME']) . '</th>';
                        }
                        ?>
                </tr>
            </thead>
            <tbody id="table-body" class="radius-sm">
                <?php
                    if ($dataResult && $dataResult->num_rows > 0) {
                        while ($row = $dataResult->fetch_assoc()) {
                            echo '<tr class="gray-color">';
                            foreach ($row as $cell) {
                                echo '<td>' . htmlspecialchars($cell) . '</td>';
                            }
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="' . $columnsResult->num_rows . '">No data found.</td></tr>';
                    }
                    ?>
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <div id="pagination" class="pagination"></div>
    </div>
</div>
<?php
} else {
    echo "No columns found or query error.";
}
?>
<script src="./js/datatable.js"></script>