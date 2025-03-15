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
                            <h1>Table Form</h1>
                        </div>
                    </div>
                    <form action="#" method="POST">
                        <div class="input-groups">
                            <div class="input-group">
                                <select name="select_db_name" id="select_db_name" class="input">
                                    <option value="">Select a table</option>
                                    <?php
                                        $select_db = "SELECT DISTINCT fname FROM collection_creator.dbtables where user_id='$user_id'";
                                        $fetch_db = $conn->query($select_db);
                                        if (!$fetch_db) {
                                            die("Query Error: " . $conn->error);    
                                        }
                                    while ($data_db = $fetch_db->fetch_array()) {
                                    ?>
                                    <option value="<?php echo htmlspecialchars($data_db[0]); ?>">
                                        <?php echo htmlspecialchars($data_db[0]); ?>
                                    </option>
                                    <?php } ?>

                                </select>
                            </div>
                            <div class="input-group">
                                <input type="text" name="table_name" class="input" placeholder="Enter Table Name"
                                    required>
                            </div>
                            <div class="input-group">
                                <input type="button" id="openModalBtn" name="dbcreate" class="button"
                                    value="Create Table">
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
                            <input type="radio" name="radio" id="PHP">
                            <span class="name">PHP</span>
                        </label>
                    </div>
                    <div class="header">
                        <!-- <span class="title">CSS</span> -->
                    </div>
                    <div class="editor-content">
                        <code class="code"><p>USE my_database</p></code>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Table Fields Modal -->
    <div class="modal-container" id="tableModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Add Table Fields</h2>
                <span class="close-btn" id="closeModal">&times;</span>
            </div>
            <div class="modal-body">
                <form id="tableFieldsForm" method="POST">
                    <div id="fieldsContainer"></div>
                    <button type="button" id="addFieldBtn">Add Field</button>
                    <div class="modal-footer">
                        <button type="submit" class="btn-close" id="createTableBtn">Create Table</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php
session_start();
include "../../Backend/Connection/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['select_db_name'], $_POST['table_name'], $_POST['table_fields'])) {
        $database_name = $conn->real_escape_string($_POST['select_db_name']);
        $tname = $conn->real_escape_string($_POST['table_name']);
        $table_fields = json_decode($_POST['table_fields'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Invalid JSON data");
        }

        $fieldDefinitions = [];
        $noLengthTypes = ['DATE', 'TEXT', 'DATETIME', 'BLOB'];

        foreach ($table_fields as $field) {
            $name = $conn->real_escape_string($field['name']);
            $type = strtoupper($conn->real_escape_string($field['type']));
            $length = $conn->real_escape_string($field['length']);

            if (in_array($type, $noLengthTypes)) {
                $fieldDefinitions[] = "`$name` $type";
            } else {
                $fieldDefinitions[] = "`$name` $type($length)";
            }
        }

        $fieldsSql = implode(", ", $fieldDefinitions);
        $createTableQuery = "CREATE TABLE `$database_name`.`$tname` ($fieldsSql)";
        $Insert_Table_Query = "INSERT INTO collection_creator.maintablename (database_name,tname,table_fields) VALUES ('$database_name', '$tname', '$fieldsSql')";

        if ($conn->query($createTableQuery)) {
            $conn->query($Insert_Table_Query);
            echo "Table created successfully!";
        } else {
            echo "Error creating table: " . $conn->error;
        }
        exit();
    }
}
?>


    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let modal = document.getElementById("tableModal");
        let openModalBtn = document.getElementById("openModalBtn");
        let closeModal = document.getElementById("closeModal");
        let fieldsContainer = document.getElementById("fieldsContainer");
        let addFieldBtn = document.getElementById("addFieldBtn");
        let tableFieldsForm = document.getElementById("tableFieldsForm");
        let dbSelect = document.getElementById("select_db_name");
        let tableNameInput = document.querySelector("input[name='table_name']");
        let codeBox = document.querySelector(".editor-content .code");
        let radioButtons = document.querySelectorAll("input[name='radio']");

        // Function to update code box
        function updateCodeBox() {
            let dbName = dbSelect.value;
            let tableName = tableNameInput.value.trim();
            let fields = document.querySelectorAll(".field-group");

            if (!dbName) {
                codeBox.innerHTML = "<p>Please select a database</p>";
                return;
            }

            // Check which radio button is selected
            if (radioButtons[0].checked) { // MYSQL Selected
                let sql = `<p>USE ${dbName};</p>\n`;

                if (tableName) {
                    sql += `<p>CREATE TABLE ${tableName} (</p>\n`;

                    let fieldStatements = [];
                    fields.forEach(field => {
                        let fieldName = field.querySelector("input[name='field_name[]']").value.trim();
                        let fieldType = field.querySelector("select[name='field_type[]']").value;
                        let fieldLength = field.querySelector("input[name='field_length[]']").value
                            .trim();

                        if (fieldName && fieldLength) {
                            fieldStatements.push(
                                `<p>${fieldName} ${fieldType}(${fieldLength})</p>`
                            );
                        }
                    });

                    sql += fieldStatements.join(",") + "<p>);</p>";
                }

                codeBox.innerHTML = sql;
            } else if (radioButtons[1].checked) { // PHP Selected
                let phpCode = `<p>&lt;?php<br>
            $conn = new mysqli("localhost", "root", "", "${dbName}");<br>
            $sql = "CREATE TABLE ${tableName} (<br>`;

                let fieldStatements = [];
                fields.forEach(field => {
                    let fieldName = field.querySelector("input[name='field_name[]']").value.trim();
                    let fieldType = field.querySelector("select[name='field_type[]']").value;
                    let fieldLength = field.querySelector("input[name='field_length[]']").value.trim();

                    if (fieldName && fieldLength) {
                        fieldStatements.push(`    ${fieldName} ${fieldType}(${fieldLength}),<br>`);
                    }
                });

                phpCode += fieldStatements.join("") + "<br>);<br>";
                phpCode += `
            if ($conn->query($sql) === TRUE) {<br>
                echo "Table created successfully";<br>
            } else {<br>
                echo "Error creating table: " . $conn->error;<br>
            }<br>
            $conn->close();<br>
            ?&gt;</p>`;

                codeBox.innerHTML = phpCode;
            }
        }

        // Event listener to update code box when radio button changes
        radioButtons.forEach(radio => {
            radio.addEventListener("change", updateCodeBox);
        });

        // Event listener to update code box when database or table name changes
        dbSelect.addEventListener("change", updateCodeBox);
        tableNameInput.addEventListener("input", updateCodeBox);

        // Add new field button
        addFieldBtn.addEventListener("click", function() {
            let fieldHTML = `
        <div class="field-group">
            <input type="text" name="field_name[]" placeholder="Field Name" required>
            <select name="field_type[]">
              <option value="VARCHAR">VARCHAR</option>
              <option value="CHAR">CHAR</option>
              <option value="TEXT">TEXT</option>
              <option value="TINYTEXT">TINYTEXT</option>
              <option value="MEDIUMTEXT">MEDIUMTEXT</option>
              <option value="LONGTEXT">LONGTEXT</option>
              <option value="BLOB">BLOB</option>
              <option value="TINYBLOB">TINYBLOB</option>
              <option value="MEDIUMBLOB">MEDIUMBLOB</option>
              <option value="LONGBLOB">LONGBLOB</option>
              <option value="INT">INT</option>
              <option value="TINYINT">TINYINT</option>
              <option value="SMALLINT">SMALLINT</option>
              <option value="MEDIUMINT">MEDIUMINT</option>
              <option value="BIGINT">BIGINT</option>
              <option value="FLOAT">FLOAT</option>
              <option value="DOUBLE">DOUBLE</option>
              <option value="DECIMAL">DECIMAL</option>
              <option value="DATE">DATE</option>
              <option value="DATETIME">DATETIME</option>
              <option value="TIMESTAMP">TIMESTAMP</option>
              <option value="TIME">TIME</option>
              <option value="YEAR">YEAR</option>
              <option value="BOOLEAN">BOOLEAN</option>
              <option value="ENUM">ENUM</option>
              <option value="SET">SET</option>
              <option value="BINARY">BINARY</option>
              <option value="VARBINARY">VARBINARY</option>

            </select>
            <input type="number" name="field_length[]" placeholder="Length" required>
            <button type="button" class="removeField">X</button>
        </div>`;

            fieldsContainer.insertAdjacentHTML("beforeend", fieldHTML);
            updateCodeBox();
        });

        // Event listener to remove a field
        fieldsContainer.addEventListener("click", function(e) {
            if (e.target.classList.contains("removeField")) {
                e.target.parentElement.remove();
                updateCodeBox();
            }
        });

        // Event listener to open modal
        openModalBtn.addEventListener("click", function(event) {
            event.preventDefault();
            if (!dbSelect.value || !tableNameInput.value) {
                alert("Please select a database and enter a table name!");
                return;
            }
            modal.style.display = "flex";
            updateCodeBox();
        });

        // Close modal
        closeModal.addEventListener("click", function() {
            modal.style.display = "none";
        });

        // Close modal if clicked outside
        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });

        document.getElementById("tableFieldsForm").addEventListener("submit", function(event) {
            event.preventDefault();
            let fieldData = [];
            let fieldGroups = document.querySelectorAll(".field-group");

            fieldGroups.forEach(function(field) {
                let fieldName = field.querySelector("input[name='field_name[]']").value.trim();
                let fieldType = field.querySelector("select[name='field_type[]']").value;
                let fieldLength = field.querySelector("input[name='field_length[]']").value
                    .trim();

                if (fieldName && fieldType) {
                    fieldData.push({
                        name: fieldName,
                        type: fieldType,
                        length: fieldLength
                    });
                }
            });

            if (fieldData.length === 0) {
                alert("Please add at least one field.");
                return;
            }

            let formData = new FormData();
            formData.append("select_db_name", document.getElementById("select_db_name").value);
            formData.append("table_name", document.querySelector("input[name='table_name']").value);
            formData.append("table_fields", JSON.stringify(fieldData));

            fetch("", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert("Table Created Successfully");
                    modal.style.display = "none";
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        });
    });
    </script>




</body>

</html>