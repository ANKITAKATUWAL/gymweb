<?php
include 'db_connection.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM register WHERE email = '$email'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            if ($password === $row['password']) {
                // Set 'user_id' and 'email' in the session
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $email;
                $_SESSION['user_contact'] = $row['phone'];
                // $_SESSION['user_contact'] = $phone;
                $_SESSION['delivery_address'] = $row['address'];
                // $_SESSION['delivery_address'] = $address;

                // Check if the user is the administrator
                if ($email === 'admin@gmail.com' && $password === 'Admin@1234') {
                    header("Location: admin_view.php");
                    exit();
                } else {
                    // For regular users
                    echo "Login successful!";
                    header("Location: home_page.php");
                    exit();
                }
            } else {
                echo "Invalid password";
            }
        } else {
            echo "Invalid email";
        }
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym & Fitness Club</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body>
    <div class="login-container">
        <div class="welcome-header">
            <h1>WELCOME!!</h1>
        </div>
        <div class="center-logo">
            <i class="fa-sharp fa-solid fa-child-reaching fa-beat-fade" style="color: #f2f7f6;"></i>
        </div>
        <h2>Gym & Fitness Club</h2>
        <form action="" method="POST" class="login-form" autocomplete="off">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit" name="submit" style="background-color: green; padding: 10px 20px; color: #fff; text-decoration: none; border-radius: 5px;">Login</button>
        </form>
        <p class="create-account-link">Don't have an account? <a href="register.php">Create New Account</a></p>
    </div>
</body>
</html>
