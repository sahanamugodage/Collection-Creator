<?php
session_start(); // Start session to access stored data

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/Login.php"); // Redirect if not logged in
    exit();
}
include "../Connection/conn.php"; 

if (!isset($_SESSION['database_name'])) {
    die("Error: No database selected.");
}
if (isset($_POST['dbName'])) {
    $_SESSION['database_name'] = $_POST['dbName'];
}
$selected_database = $_SESSION['database_name'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Table Names</title>
    <link rel="stylesheet" href="../Styles/color.style.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>

<body>
    <?php include "../Components/main_header.php"; 
  
    ?>
    <div class="container">
        <div class="content">
            <div class="inside-content">
                <div class="inside-body">
                    <div class="table-cards" data-modal="edit-modal">
                        <?php 
    $find_tb_Query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$selected_database' AND TABLE_TYPE = 'BASE TABLE'";
    $fetch_tb = $conn->query($find_tb_Query);
    while($get_tb = $fetch_tb->fetch_array()){ 
        $table_name = $get_tb[0];
                         
        
        $column_count_query = "SELECT COUNT(*) AS column_count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$selected_database' AND TABLE_NAME = '$table_name'";
        $column_result = $conn->query($column_count_query);
        $column_count = $column_result->fetch_assoc()['column_count'];
        
        $row_count_query = "SELECT COUNT(*) AS row_count FROM `$selected_database`.`$table_name`";
        $row_result = $conn->query($row_count_query);
        $row_count = $row_result->fetch_assoc()['row_count'];
            ?>
                        <div class="table-card">
                            <div class="table-card-header">
                                <h3><?php echo $table_name; ?></h3>
                            </div>
                            <div class="table-card-body">
                                <p><strong>Columns:</strong> <?php echo $column_count; ?></p>
                                <p><strong>Rows:</strong> <?php echo $row_count; ?></p>
                            </div>
                            <div class="table-card-footer">
                                <div class="table-icon modal-trigger">
                                    <i class="fas fa-eye" onclick="openInNewTab('<?php echo $table_name; ?>')"></i>
                                </div>
                                <div class="table-icon">
                                    <i class="fas fa-plus" data-table-name="<?php echo $table_name; ?>"
                                        onclick="openModal(event)"></i>
                                </div>

                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-container hidden">
        <div class="modal">
            <div class="modal-header">
                <h2>Table Data Preview</h2>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">

                <table class="modal-table">
                    <thead>

                    </thead>
                    <tbody>

                    </tbody>
                </table>
                <p id="table-name-preview"></p>
            </div>
            <div class="modal-footer">
                <button class="btn-close">Close</button>
            </div>
        </div>
    </div>

    <script>
    function closeModal() {
        const modalContainer = document.querySelector('.modal-container');
        modalContainer.style.display = 'none';
    }

    document.querySelector('.close-btn').addEventListener('click', closeModal);
    document.querySelector('.btn-close').addEventListener('click', closeModal);

    document.querySelector('.modal-container').addEventListener('click', (e) => {
        if (e.target === document.querySelector('.modal-container')) {
            closeModal();
        }
    });

    function openInNewTab(form_name) {
        window.open("dynamic_form.php?table_name=" + form_name, "_blank");
    }

    function openModal(event) {
        const tableName = event.target.getAttribute('data-table-name');

        // Show the modal
        const modalContainer = document.querySelector('.modal-container');
        modalContainer.style.display = 'flex';

        // Update the modal title with the table name
        const tableNamePreview = document.getElementById('table-name-preview');
        // tableNamePreview.textContent = "Table Name: " + tableName;

        // Fetch table data and columns via AJAX
        fetch(`fetch_table_data.php?table=${tableName}`)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('.modal-table tbody');
                const tableHeader = document.querySelector('.modal-table thead');

                // Clear previous content
                tableBody.innerHTML = "";
                tableHeader.innerHTML = ""; // Clear previous headers

                if (data.data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='100%'>No data available</td></tr>";
                    return;
                }

                // Create headers dynamically
                let headerHTML = "<tr>";
                data.columns.forEach(column => {
                    headerHTML += `<th>${column}</th>`;
                });
                headerHTML += "</tr>";
                tableHeader.innerHTML = headerHTML;

                // Populate table with data
                data.data.forEach(row => {
                    let rowHTML = "<tr>";
                    for (const column in row) {
                        rowHTML += `<td>${row[column]}</td>`;
                    }
                    rowHTML += "</tr>";
                    tableBody.innerHTML += rowHTML;
                });
            })
            .catch(error => console.error('Error fetching table data:', error));
    }
    </script>
</body>

</html>