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
    <title>Document</title>
    <link rel="stylesheet" href="../Styles/color.style.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>

<body>
    <?php include "../Components/main_header.php" ?>

    <div class="container">
        <div class="content main-forms-content">
            <div class="cards">
                <div class="card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="header-text">
                            <h1>Database Form</h1>
                        </div>
                    </div>
                    <form action="#" method="POST">
                        <div class="input-groups">
                            <div class="input-group">
                                <input type="text" name="db_name" id="bdbname" class="input"
                                    placeholder="Enter Database name">
                            </div>
                            <div class="input-group">
                                <textarea rows='5' name="database_description" placeholder=" Enter Database Description"
                                    class='textarea'></textarea>

                            </div>
                            <div class="input-group">
                                <input type="submit" name="dbtables" class="button" value="Create Database">
                            </div>
                        </div>
                    </form>
                </div>



                <div class="code-editor">
                    <div class="radio-inputs">
                        <label class="radio">
                            <input type="radio" name="radio" checked="">
                            <span class="name">MYSQL</span>
                        </label>
                        <label class="radio">
                            <input type="radio" name="radio">
                            <span class="name">PHP</span>
                        </label>
                    </div>
                    <div class="header">
                        <!-- <span class="title">CSS</span> -->
                    </div>
                    <div class="editor-content">
                        <code class="code"><p>CREATE DATABASE my_database</p></code>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['dbtables'])) {
            $db_name = $_POST['db_name'];
            $database_description = $_POST['database_description'];

    if (empty($db_name) || empty($database_description)) {
        echo '<script>
            alert("All fields are required");
            </script>';
    } else {
            $find_db_Exsist = "SELECT fname FROM collection_creator.dbtables WHERE fname = ?";
            $stmt = $conn->prepare($find_db_Exsist);
            $stmt->bind_param("s", $db_name);
            $stmt->execute();
            $fetch_db_Exsist = $stmt->get_result();

            if ($fetch_db_Exsist->num_rows > 0) { 
                echo '<script>alert("Give Another Name!");</script>';
            } else{
            $insertQuery = "INSERT INTO collection_creator.dbtables (fname,database_description,user_id) VALUES ('$db_name', '$database_description','$user_id')";
            
            if ($conn->query($insertQuery)) {
                echo '<script>alert("Data Insert successful!");</script>';
                $create_db_Query = "CREATE DATABASE $db_name";
                if($conn->query($create_db_Query)){
                    echo '<script>console.log("Database Created successful!");</script>';
                }
            } else {
                echo '<script>alert("Error inserting data!");</script>';
            }
        }
          } 
    }
}
                ?>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let inputField = document.getElementById("bdbname");
        let radioButtons = document.querySelectorAll("input[name='radio']");
        let codeBlock = document.querySelector(".editor-content .code");

        function updateCode() {
            let dbName = inputField.value.trim() || "my_database";

            if (radioButtons[0].checked) { // MYSQL Selected
                codeBlock.innerHTML = `<p>CREATE DATABASE ${dbName};</p>`;
            } else if (radioButtons[1].checked) { // PHP Selected
                codeBlock.innerHTML = `<p>&lt;?php<br>
                $conn = new mysqli("localhost", "root", "", "");<br>
                $sql = "CREATE DATABASE ${dbName}";<br>
                if ($conn->query($sql) === TRUE) {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;echo "Database created successfully";<br>
                } else {<br>
                &nbsp;&nbsp;&nbsp;&nbsp;echo "Error creating database: " . $conn->error;<br>
                }<br>
                $conn->close();<br>
                ?&gt;</p>`;
            }
        }

        // Listen for input changes
        inputField.addEventListener("input", updateCode);

        // Listen for radio button changes
        radioButtons.forEach(radio => {
            radio.addEventListener("change", updateCode);
        });

        // Initial setup
        updateCode();
    });
    </script>



</body>

</html>