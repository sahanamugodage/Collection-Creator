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
    <title>Dynamic Form</title>
    <link rel="stylesheet" href="../Styles/color.style.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
    <link rel="stylesheet" href="../Styles/main.style.css">
    <link rel="stylesheet" href="../Styles/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-folder"></i> &ensp;Form Editor</h2>
        <ul id="sidebar-properties">
            <!-- Properties will be dynamically added here -->
        </ul>
        <div class="sidebar-bottom">
            <button class="save-btn" onclick="downloadAllFiles()"><i class="fas fa-download"></i></button>
            <button class="save-btn" onclick="openModal(event)"><i class="fas fa-eye"></i></button>
        </div>
    </div>
    <?php include "../Components/main_header.php" ?>

    <!-- Main content -->
    <div class="main-content">
        <?php
        if (!isset($_SESSION['database_name'])) {
            die("Error: No database selected.");
        }
        if (isset($_POST['dbName'])) {
            $_SESSION['database_name'] = $_POST['dbName'];
        }
        $selected_database = $_SESSION['database_name'];

        if (isset($_GET['table_name'])) {
            $table_name = $_GET['table_name'];
        } else {
            die("Error: No table name specified.");
        }

        // Fetch columns for the selected table
        $columns_query = "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_KEY 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = '$selected_database' 
                        AND TABLE_NAME = '$table_name'";

        $columns_result = $conn->query($columns_query);

        $fields = [];
        while ($column = $columns_result->fetch_assoc()) {
            if ($column['COLUMN_KEY'] === 'PRI') {
                continue;
            }
            $fields[] = $column;
        }

        $total_fields = count($fields);
        $grid_class = $total_fields > 5 ? 'two-column' : '';
        $max_width = $total_fields < 5 ? 'max-width-form-card' : '';
        ?>

        <div class="dynamic-cards">
            <div class="dynamic-card <?php echo $max_width; ?>">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="header-text">
                        <h1 id="form-header"><?php echo $table_name; ?> Form</h1>
                    </div>
                </div>

                <?php
                echo "<form action='process_form.php' method='POST' class='dynamic-form'>";
                echo "<div class='input-groups $grid_class'>";

                foreach ($fields as $column) {
                    $column_name = $column['COLUMN_NAME'];
                    $data_type = $column['DATA_TYPE'];

                    echo "<div class='input-group'>";
                    echo "<label for='$column_name'>$column_name</label>";

                    if ($data_type == 'int') {
                        echo "<input class='input' type='number' name='$column_name' id='$column_name' placeholder='Enter $column_name' required>";
                    } elseif ($data_type == 'varchar' || $data_type == 'text') {
                        echo "<input class='input' type='text' name='$column_name' id='$column_name' placeholder='Enter $column_name' required>";
                    } elseif ($data_type == 'date') {
                        echo "<input class='input' type='date' name='$column_name' id='$column_name' required>";
                    } elseif ($data_type == 'datetime') {
                        echo "<input class='input' type='datetime-local' name='$column_name' id='$column_name' required>";
                    } else {
                        echo "<input class='input' type='text' name='$column_name' id='$column_name' placeholder='Enter $column_name' required>";
                    }

                    echo "</div>";
                }

                echo "</div>"; // End input-groups
                ?>

                <div class="button-group">
                    <button class='button' id="form-submit-button" type='submit'>Submit</button>
                </div>

                <?php
                echo "</form>";
                ?>
            </div>
        </div>
    </div>


    <div class="modal-container hidden">
        <div class="modal">
            <div class="modal-header">
                <h2>Code Preview</h2>
                <span class="close-btn">&times;</span>
            </div>
            <div class="modal-body">
                <code id="php-code">

                </code>
            </div>
            <div class="modal-footer">
                <!-- <button class="btn-close">Close</button> -->
            </div>
        </div>
    </div>

    <script>
    // JavaScript to handle input click and display properties in the sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.input');
        const sidebarProperties = document.getElementById('sidebar-properties');
        const formHeader = document.getElementById('form-header');
        const submitButton = document.getElementById('form-submit-button');

        // Function to update the sidebar with input properties
        function showInputProperties(input) {
            const label = input.previousElementSibling.textContent;
            const placeholder = input.getAttribute('placeholder') || '';
            const type = input.getAttribute('type');

            sidebarProperties.innerHTML = `
        <li><strong>Change Label:</strong> <input class='input' type="text" id="sidebar-label" value="${label}" /></li>
        <li><strong>Change Placeholder:</strong> <input class='input' type="text" id="sidebar-placeholder"
                value="${placeholder}" /></li>
        <li><strong>Change Input Type:</strong>
            <select id="sidebar-type" class="input">
                <option value="text" ${type==='text' ? 'selected' : '' }>Text</option>
                <option value="number" ${type==='number' ? 'selected' : '' }>Number</option>
                <option value="date" ${type==='date' ? 'selected' : '' }>Date</option>
                <option value="datetime-local" ${type==='datetime-local' ? 'selected' : '' }>Datetime</option>
                <option value="email" ${type==='email' ? 'selected' : '' }>Email</option>
                <option value="password" ${type==='password' ? 'selected' : '' }>Password</option>
                <option value="file" ${type==='file' ? 'selected' : '' }>file</option>
            </select>
        </li>
        <li><button id="sidebar-save">Save Changes</button></li>
        <li><button id="sidebar-remove-field">Remove field</button></li>

        `;

            // Add event listener to the Save button
            const saveButton = document.getElementById('sidebar-save');
            saveButton.addEventListener('click', function() {
                // Update the label, placeholder, and type in the form
                const newLabel = document.getElementById('sidebar-label').value;
                const newPlaceholder = document.getElementById('sidebar-placeholder').value;
                const newType = document.getElementById('sidebar-type').value;

                input.previousElementSibling.textContent = newLabel;
                input.setAttribute('placeholder', newPlaceholder);
                input.setAttribute('type', newType);

                // Clear the sidebar
                sidebarProperties.innerHTML = '';
            });



            const removeFieldButton = document.getElementById('sidebar-remove-field');
            removeFieldButton.addEventListener('click', function() {
                // Remove the input field and its label from the DOM
                const inputGroup = input.closest('.input-group');
                if (inputGroup) {
                    inputGroup.remove();
                }

                // Clear the sidebar
                sidebarProperties.innerHTML = '';
            });
        }


        // Function to update the sidebar with form header and button properties
        function showFormProperties() {
            sidebarProperties.innerHTML = `
        <li><strong>Change Form Header:</strong> <input class='input' type="text" id="sidebar-header"
                value="${formHeader.textContent}" /></li>
        <li><strong>Change Button Text:</strong> <input class='input' type="text" id="sidebar-button-text"
                value="${submitButton.textContent}" /></li>
        <li><strong>Change Button Color:</strong> <input class='input' type="color" id="sidebar-button-color"
                value="#007bff" /></li>
        <li><button id="sidebar-save-form">Save Changes</button></li>
        `;

            // Add event listener to the Save button
            const saveButton = document.getElementById('sidebar-save-form');
            saveButton.addEventListener('click', function() {
                // Update the form header, button text, and button color
                const newHeader = document.getElementById('sidebar-header').value;
                const newButtonText = document.getElementById('sidebar-button-text').value;
                const newButtonColor = document.getElementById('sidebar-button-color').value;

                formHeader.textContent = newHeader;
                submitButton.textContent = newButtonText;
                submitButton.style.backgroundColor = newButtonColor;

                // Clear the sidebar
                sidebarProperties.innerHTML = '';
            });
        }

        // Add click event listeners to input fields
        inputs.forEach(input => {
            input.addEventListener('click', function() {
                showInputProperties(input);
            });
        });

        // Add click event listener to the form header
        formHeader.addEventListener('click', function() {
            showFormProperties();
        });

        // Add click event listener to the submit button
        submitButton.addEventListener('click', function() {
            showFormProperties();
        });
    });


    function openModal(event) {
        const tableName = event.target.getAttribute('data-table-name'); // Get the table name from the clicked element
        const phpCode = generatePHPCode();
        const codeElement = document.getElementById('php-code');
        codeElement.textContent = phpCode;
        // Show the modal
        const modalContainer = document.querySelector('.modal-container');
        modalContainer.style.display = 'flex';

    }

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

    function generatePHPCode() {
        const formHeader = document.getElementById('form-header').textContent;
        const submitButton = document.getElementById('form-submit-button').textContent;
        const inputs = document.querySelectorAll('.input-group');


        let phpCode = `
        <?php echo "<?php" ?>

        \$host = 'localhost';
        \$hostuser = 'root';
        \$hostuserpass = 'root';
        \$dbName = '<?php echo $selected_database ?>';
        \$SelectedtableName = '<?php echo $table_name ?>';

        \$conn = new mysqli(\$host, \$hostuser, \$hostuserpass, \$dbName);
        if (\$conn->connect_error) {
            die(json_encode(['status' => 'error', 'message' => 'Connection Error: ' . $conn->connect_error]));
        }
        <?php echo "?>" ?>

        <div class=\"container\">
            <div class=\"content\">
                <div class=\"cards\">
                    <div class=\"card\">
                        <div class=\"card-header\">
                            <div class=\"header-icon skeleton\">
                                <i class=\"fas fa-database purple-hover\"></i>
                            </div>
                            <div class=\"header-text skeleton\">
                                <h1>${formHeader}</h1>
                            </div>
                        </div>

                        <form action='#' method='POST' class='dynamic-form'>
                            <div class='input-groups'>`;

        inputs.forEach(inputGroup => {
            const label = inputGroup.querySelector('label').textContent;
            const input = inputGroup.querySelector('input');
            const inputName = input.getAttribute('name');
            const inputType = input.getAttribute('type');
            const placeholder = input.getAttribute('placeholder') || '';

            phpCode += `
                                <div class='input-group'>
                                    <label for='${inputName}'>${label}</label>
                                    <input class='input' type='${inputType}' name='${inputName}' id='${inputName}'
                                        placeholder='${placeholder}' required>
                                </div>
                                `;
        });

        phpCode += `
                            </div>
                            <div class='button-group'>
                                <button class='button' name=${"'<?php echo $table_name ?>'"}
                                    type='submit'>${submitButton}</button>
                            </div>
                        </form>
                   </div>
              </div>
          </div>
      </div>
                        `;

        phpCode += `
<?php echo "<?php" ?>

    if (\$_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset(\$_POST['<?php echo $table_name ?>'])) {`



        let inputNames = [];
        inputs.forEach(inputGroup => {
            const input = inputGroup.querySelector('input');
            const inputName = input.getAttribute('name');
            inputNames.push(inputName);
        });

        phpCode += `
        // Check if any required field is empty
        if (empty(` + inputNames.map(name => `\$_POST['${name}']`).join(') || empty(') + `)) {
            echo "alert('Please fill in all required fields');";
        } else {`;

        phpCode += `
            // Prepare SQL query
            \$sql = "INSERT INTO <?php echo $table_name; ?> (` + inputNames.join(', ') + `) VALUES (` +
            inputNames
            .map(name =>
                `'\$${name}'`).join(', ') + `)";
            // Execute the query
            if (\$conn->query(\$sql) === TRUE) {
                echo json_encode(['status' => 'success', 'message' => 'Record inserted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
                    }
                }
            }
        }
?>`;


        return phpCode;
    }

    function downloadFile(filename, content, type) {
        // Create a Blob with the PHP 
        const blob = new Blob([content], {
            type: type
        });

        // Create a downloadable link
        const url = URL.createObjectURL(blob);

        // Create an anchor element and trigger the download
        const a = document.createElement("a");
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();

        // Clean up
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    function downloadAllFiles() {
        // Generate the PHP code
        const phpCode = generatePHPCode();

        // Download the PHP file
        downloadFile("form_code.php", phpCode, "application/x-httpd-php");
    }
    </script>

    <script src="../Scripts/code.js"></script>

</body>

</html>