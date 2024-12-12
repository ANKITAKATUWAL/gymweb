<?php
include 'db_connection.php';


if (isset($_POST['submit'])) {
    echo "Form submitted";
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

   // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format");
}

if (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/", $password)) {
    die("Password must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters");
}

if (!is_numeric($age) || $age < 20 || $age > 80) {
    die("Invalid age");
}

if (!is_numeric($weight) || $weight < 40 || $weight > 150) {
    die("Invalid weight");
}

if (!preg_match("/^[0-9]{10}$/", $phone)) {
    die("Phone number should be 10 digits");
}

if (!preg_match("/^[a-zA-Z0-9\-]+,[a-zA-Z0-9\-]+$/", $address)) {
    die("Address should be in the format 'area-number,city' using letters, numbers, and hyphens");
}

// Hash password
//$hashed_password = password_hash($password, PASSWORD_DEFAULT);



    $sql = "INSERT INTO register (name, email, password, age, gender, weight,phone,address) VALUES ('$name', '$email', '$password', '$age', '$gender', '$weight','$phone','$address')";
    $result = mysqli_query($con, $sql);

    if ($result) {
        echo "Account created sucessfully";
        header("Location: login.php");
    } else {    
        die(mysqli_error($con));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Gym & Fitness Club</title>
  <link rel="stylesheet" href="css/register.css">
</head>

<body>
  <div class="register-container">
    <h1>Create New Account</h1>
    <form  action="" method="POST" class="register-form" autocomplete="off">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" pattern="[A-Za-z]+" title="Must contain at only letters" required>
      
      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>
      
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters" required>
      
      <label for="age">Age:</label>
      <input type="number" id="age" name="age" min="20" max="80" required>
      <label for="phone">Phone:</label>
      <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" title="Phone number should be 10 digits" required>      
      <label for="address">Address:</label>
      <input id="address" name="address" pattern="[a-zA-Z0-9\-]+,[a-zA-Z0-9\-]+" title="Address should be in the format 'area-number,city' using letters, numbers, and hyphens" required>  
      <div class="gender">
        <label for="gender">Gender:</label>
        <br>
        <select id="gender" name="gender" required>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select>
      </div>

      <br>

      <label for="weight">Weight:</label>
      <input type="number" id="weight" name="weight" min="50" max="300" required>      
      <button type="submit" name="submit">Create Account</button>
    </form>
    <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
   