<?php
session_start(); // Start session to access stored data

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Auth/Login.php"); // Redirect if not logged in
    exit();
}
include "../Connection/conn.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
    <link rel="stylesheet" href="../Styles/color.style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

</head>

<body>
    <?php include "../Components/main_header.php" ?>
    <div class="container">
        <div class="content">
            <div class="inside-content">
                <div class="inside-header">
                    <h2>Manage Your Databases</h2>
                </div>
                <div class="inside-body">
                    <!-- First Card (Plus Icon) -->
                    <div class="database-option-card plus-card">
                        <i class="fas fa-plus"></i>
                    </div>

                    <div class="database-option-card">
                        <!-- Card Header -->
                        <div class="card-header">
                            Customer Table
                        </div>
                        <div class="table-card-body db-card-body">
                            <p><strong>Total Fields</strong> 10</p>
                        </div>
                        <div class="table-card-footer">
                            <div class="table-icon modal-trigger">

                            </div>
                            <div class="table-icon db-card-icon ellipsis-wrapper">
                                <i class="fas fa-ellipsis-v ellipsis-icon"></i>

                                <!-- Dropdown Menu -->
                                <div class="dropdown-menu">
                                    <div class="card">
                                        <ul class="list">
                                            <li class="element"><i class="fas fa-edit"></i> Edit</li>
                                            <li class="element"><i class="fas fa-user-plus"></i> Add User</li>
                                            <div class="separator"></div>
                                            <li class="element"><i class="fas fa-cog"></i> Settings</li>
                                            <li class="element delete"><i class="fas fa-trash"></i> Delete</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll(".ellipsis-icon").forEach(icon => {
            icon.addEventListener("click", (event) => {
                event.stopPropagation(); // Prevent event bubbling
                const wrapper = icon.closest(".ellipsis-wrapper");
                const dropdown = wrapper.querySelector(".dropdown-menu");

                // Hide all dropdowns first
                document.querySelectorAll(".dropdown-menu").forEach(menu => {
                    if (menu !== dropdown) menu.style.display = "none";
                });

                // Toggle visibility
                dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener("click", (event) => {
            document.querySelectorAll(".dropdown-menu").forEach(menu => {
                if (!menu.contains(event.target) && !event.target.classList.contains(
                        "ellipsis-icon")) {
                    menu.style.display = "none";
                }
            });
        });
    });
    </script>
</body>

</html>