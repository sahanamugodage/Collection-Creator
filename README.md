# Collection Creator

Collection Creator is a web-based tool that allows users to create and manage databases, tables, and table fields dynamically. Users can also generate MySQL and PHP code for their database structures, view and modify table data, and create fully customizable forms based on table fields.

## Features

### **Database & Table Management**
- Create databases and tables dynamically.
- Define table fields with data types and constraints.
- View generated MySQL and PHP code in a separate code box.

### **Table Data Viewing & Management**
- View data of the created tables in a structured format.
- Perform CRUD (Create, Read, Update, Delete) operations on table data.

### **Dynamic Form Generator**
- Automatically generate forms based on table field names.
- Customize forms by:
  - Changing input box placeholders.
  - Modifying input labels.
  - Editing button text and color.
  - Renaming the form.
  - Removing unnecessary fields.
- Get the generated PHP and HTML form submission code, including all customizations.

## Installation

### **Prerequisites**
Ensure you have the following installed on your system:
- PHP 7.4 or later
- MySQL or MariaDB
- Apache or any local server (XAMPP, MAMP, or WAMP)

### **Setup**
1. Clone the repository:
   ```sh
   git clone https://github.com/sahanamugodage/Collection-Creator.git
   ```
2. Navigate to the project directory:
   ```sh
   cd Collection-Creator
   ```
3. Configure your database connection in `Connection/conn.php`:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "your_database_name";

   $conn = new mysqli($servername, $username, $password, $dbname);

   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ```
4. Start your local server and access the project via `http://localhost/Collection-Creator`.

## Usage

1. **Create a Database & Table**
   - Navigate to the "Database" section and create a new database.
   - Add tables and define fields with appropriate data types.
   - View generated MySQL and PHP code for reference.

2. **View & Manage Data**
   - Select a table to view its records.
   - Perform CRUD operations on the data.

3. **Generate & Customize Forms**
   - Generate a form based on a selected table.
   - Modify placeholders, labels, button text, and colors.
   - Remove fields if necessary.
   - Copy the automatically generated PHP and HTML form code for easy integration.

## Screenshots
(Add screenshots of the UI to showcase functionality.)

## Contribution
Contributions are welcome! Follow these steps to contribute:
1. Fork the repository.
2. Create a new branch (`feature-new-functionality`).
3. Commit your changes.
4. Push to the branch and submit a pull request.

## License
This project is licensed under the MIT License.

## Contact
For any questions or suggestions, feel free to reach out:
- GitHub: [sahanamugodage](https://github.com/sahanamugodage)
