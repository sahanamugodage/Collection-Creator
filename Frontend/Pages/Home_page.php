<?php
session_start(); // Start session to access stored data

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/Login.php"); // Redirect if not logged in
    exit();
}
include "../Connection/conn.php"; 
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management</title>
    <!-- Add Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
</head>

<body>
    <!-- Header Section -->
    <?php include "../Components/main_header.php" ?>


    <!-- Main Content -->
    <div class="container">
        <div class="content">
            <div class="inside-content">
                <div class="inside-header">
                    <h2>Manage Your Databases</h2>
                </div>
                <div class="inside-body">
                    <div class="mini-cards" id="drag-drop">
                        <!-- Database Cards -->
                        <?php
                         $find_db = "SELECT fname FROM collection_creator.dbtables where user_id='$user_id'"; 
                         $fetch_db = $conn->query($find_db);
                         while($get_db = $fetch_db->fetch_array()){ 
                         ?>
                        <div class="mini-card" draggable="true">
                            <div class="mini-card-header">
                                <p><?php echo($get_db[0]); ?></p>
                            </div>
                            <div class="mini-card-body">

                            </div>
                            <div class="mini-card-footer">
                                <?php 
                                $database_name = $get_db[0]; 
                                $_SESSION['database_name'] = $database_name;
                                ?>
                                <div class="mini-icon table-icon" data-tooltip="View Tables"
                                    data-db-name="<?php echo htmlspecialchars($database_name); ?>">
                                    <i class="fas fa-table"></i>
                                </div>
                            </div>
                        </div>

                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    const cards = document.querySelectorAll('.table-icon');
    cards.forEach((card) => {
        card.addEventListener('click', () => {
            const dbName = card.getAttribute('data-db-name');
            fetch('tables.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'dbName=' + encodeURIComponent(dbName),
            }).then(() => {
                window.location.href = 'tables.php';
            });
        });
    });
    </script>
</body>

</html>