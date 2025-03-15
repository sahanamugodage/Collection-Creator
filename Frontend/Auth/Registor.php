<?php
include "../Connection/conn.php"; 

if ($_SERVER['REQUEST_METHOD'] == "POST") {   
    $reg_user_name = trim($_POST['registor_user_name']); 
    $reg_email = trim($_POST['registor_user_email']);
    $reg_password = trim($_POST['registor_user_password']);
    $confirm_password = trim($_POST['confirm_password']);

    $errors = [];

    // Validate email with regex
    if (!filter_var($reg_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password: At least one letter, one number, min 8 chars
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $reg_password)) {
        $errors[] = "Password must contain at least 8 characters, including letters and numbers.";
    }

    // Check if passwords match
    if ($reg_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = password_hash($reg_password, PASSWORD_DEFAULT); // Hash password

        $insert_query = "INSERT INTO collection_creator.users (username, useremail, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sss", $reg_user_name, $reg_email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='../Auth/Login.php';</script>";
        } else {
            echo "<script>alert('Error: Could not register. Try again.');</script>";
        }

        $stmt->close();
    } else {
        // Show validation errors
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../Styles/main.style.css">
    <link rel="stylesheet" href="../Styles/components.style.css">
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="Login-Form-container">
                <form class="login-form" method="POST" action="">
                    <h2>Register For Collection Editor</h2>

                    <div class="input-group">
                        <label for="username">User Name</label>
                        <input type="text" name="registor_user_name" id="username" class="input"
                            placeholder="Enter your username" required>
                    </div>

                    <div class="input-group">
                        <label for="email">User Email</label>
                        <input type="email" name="registor_user_email" id="email" class="input"
                            placeholder="Enter your email" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="registor_user_password" class="input" id="password"
                            placeholder="Enter your password" required>
                    </div>

                    <div class="input-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" class="input" id="confirm_password"
                            placeholder="Re-enter your password" required>
                    </div>

                    <button type="submit" class="login-btn">Register</button>

                    <div class='navigate-forms'>
                        <p>Already Have An Account? <a href="../Auth/Login.php">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>