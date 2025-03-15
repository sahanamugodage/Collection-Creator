<?php 
session_start();
include "../Connection/conn.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = $_POST['log_user_name'];
    $password = $_POST['log_user_password'];

    // Check if the user exists in the database
    $query = "SELECT * FROM collection_creator.users WHERE useremail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the user exists, verify password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Start session and save user data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../Pages/Home_page.php"); 
            exit();
        } else {
            echo "<script>alert('Incorrect password or User Name!')</script>";
        }
    } else {
        echo "<script>alert('User not found!')</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="../Styles/main.style.css">
    <link rel=" stylesheet" href="../Styles/components.style.css">
</head>

<body>
    <div class="container">
        <div class="content">
            <div class="Login-Form-container">
                <form class="login-form" method="POST">
                    <h2>Login For Collection Editor</h2>
                    <div class="input-group">
                        <label for="username">User Email</label>
                        <input type="email" id="username" name="log_user_name" class="input"
                            placeholder="Enter your username" required>
                    </div>
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" name="log_user_password" class="input" id="password"
                            placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="login-btn" id="login-btn">Login</button>
                    <div class='navigate-forms'>
                        <p>Do not Have An Account <a href="../Auth/Registor.php">Registor</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>