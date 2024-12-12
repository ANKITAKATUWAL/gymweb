<?php
include 'db_connection.php';

if(isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    
    // Fetch user data based on UID
    $sql = "SELECT * FROM register WHERE id='$uid'";
    $result = mysqli_query($con, $sql);
    
    if($result) {
        $row = mysqli_fetch_assoc($result);
        
        if(!$row) {
            echo "User not found!";
            exit();
        }
    } else {
        echo "Error: " . mysqli_error($con);
        exit();
    }
}  else {
    echo "User ID not provided!";
    exit();
}

if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Update user data in the database
    $sql = "UPDATE register SET name='$name', password='$password', age='$age', gender='$gender', weight='$weight', phone='$phone', address='$address' WHERE id='$uid'";
    $result = mysqli_query($con, $sql);
    
    if($result) {
        echo "User data updated successfully!";
    } else {
        echo "Error updating user data: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User - Fitness and Gym Center</title>
    <link rel="stylesheet" href="view_update_delete.css"> 
</head>
<style>
    /* CSS for the update user form */
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
}

.update-user-container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.update-user-container h1 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

input, select {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

input:focus, select:focus {
    border-color: #007bff;
    outline: none;
}

button {
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #0056b3;
}

a {
    display: block;
    text-align: center;
    margin-top: 20px;
    padding: 10px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

a:hover {
    background-color: #0056b3;
}

@media (max-width: 600px) {
    .update-user-container {
        padding: 15px;
    }
    
    input, select, button, a {
        font-size: 14px;
        padding: 8px;
    }
}

</style>
<body>
    <div class="update-user-container">
        <h1>Update User</h1>
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?php echo $row['password']; ?>" required>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?php echo $row['age']; ?>" required>
            
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="male" <?php if($row['gender'] == 'male') echo 'selected'; ?>>Male</option>
                <option value="female" <?php if($row['gender'] == 'female') echo 'selected'; ?>>Female</option>
                <option value="other" <?php if($row['gender'] == 'other') echo 'selected'; ?>>Other</option>
            </select>
            
            <label for="weight">Weight:</label>
            <input type="number" id="weight" name="weight" value="<?php echo $row['weight']; ?>" required>
            <label for="phone">Contact:</label>
            <input type="number" id="phone" name="phone" value="<?php echo $row['phone']; ?>" required>
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo $row['address']; ?>" required><br>
            
            <button type="submit" name="update">Update</button>
        </form>
        <p><a href="admin_view.php" style="background-color: #007bff; padding: 10px 20px; color: #fff; text-decoration: none; border-radius: 5px;">Back to Admin View</a></p>
    </div>
</body>
</html>
