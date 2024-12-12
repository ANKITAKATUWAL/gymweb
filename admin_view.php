<?php
include 'db_connection.php';

// Fetch all user data
$sql = "SELECT * FROM register";
$result = mysqli_query($con, $sql);

if (!$result) {
    echo "Error: " . mysqli_error($con);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin View - Fitness and Gym Center</title>
    <link rel="stylesheet" href="css/view_update_delete.css"> 
    <style>        
    .admin-view-container p.bottom-links {
            text-align: center; /* Center align the links */
            margin-top: 20px; /* Add some top margin */
        }

        .admin-view-container p.bottom-links a {
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            margin: 0 5px; /* Add margin between links */
            display: inline-block;
            color: #fff;
            transition: background-color 0.3s;
        }

        .admin-view-container a.logout {
            background-color: #dc3545; /* Red background color for logout */
        }

        .admin-view-container p.bottom-links a.manage {
            background-color: #28a745; /* Green background color for manage */
        }

        .admin-view-container p.bottom-links a:hover {
            background-color: #555; /* Darker shade on hover */
        }
        </style>
</head>
<body>
    <div class="admin-view-container">
        <h1>User Account View</h1>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Weight</th>
                <th>Action</th>
                <th>Contact</th>
                <th>Address</th>
            </tr>
            <?php
            // Loop through each row of user data
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['password']."</td>";
                echo "<td>".$row['age']."</td>";
                echo "<td>".$row['gender']."</td>";
                echo "<td>".$row['weight']."</td>";
                echo "<td>".$row['phone']."</td>";
                echo "<td>".$row['address']."</td>";
                // Pass UID to update.php
                echo "<td><a href='update.php?uid=".$row['id']."'>Update</a>|<a href='#' onclick='confirmDelete(".$row['id'].")' style='background-color: #dc3545;'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </table>
        <p><a href="login.php" class="logout">Logout</a></p>
        
        <p class="bottom-links">
            <a href="manage_membership.php" class="manage">Manage Membership Plans</a> 
        </p>
        
    </div>
    <script>
        function confirmDelete(uid) {
            if(confirm("Are you sure you want to delete this user?")) {
                window.location.href = "delete.php?uid=" + uid;
            }
        }
    </script>
</body>
</html>

<?php
mysqli_close($con);
?>
