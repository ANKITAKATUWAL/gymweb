<?php
include 'db_connection.php';

// Fetch all user memberships
$membership_sql = "SELECT * FROM membership";
$membership_result = mysqli_query($con, $membership_sql);

if (!$membership_result){
    echo "Error: " . mysqli_error($con);
}

// Fetch all membership plans
$plan_sql = "SELECT * FROM membership_plans";
$plan_result = mysqli_query($con, $plan_sql);

if (!$plan_result) {
    echo "Error: " . mysqli_error($con);
}

// Handle delete membership
if (isset($_GET['delete_membership_id'])) {
    $id = $_GET['delete_membership_id'];
    $delete_sql = "DELETE FROM membership WHERE id = $id";
    if (mysqli_query($con, $delete_sql)) {
        header("Location: manage_membership.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Handle add new membership plan
if (isset($_POST['add_plan'])) {
    $plan_name = $_POST['plan_name'];
    $plan_price = $_POST['plan_price'];
    $plan_features = $_POST['plan_features'];

    $add_plan_sql = "INSERT INTO membership_plans (name, price, features) VALUES ('$plan_name', '$plan_price', '$plan_features')";
    if (mysqli_query($con, $add_plan_sql)) {
        header("Location: manage_membership.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Handle delete membership plan
if (isset($_GET['delete_plan_id'])) {
    $id = $_GET['delete_plan_id'];
    $delete_plan_sql = "DELETE FROM membership_plans WHERE id = $id";
    if (mysqli_query($con, $delete_plan_sql)) {
        header("Location: manage_membership.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Membership - Fitness and Gym Center</title>
    <!--<link rel="stylesheet" href="css/view_update_delete.css">-->
    <style>
        /* CSS for view_update_delete.css */
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            background: url(css/image/background2.jpg) center/cover no-repeat fixed; /* Assuming image/background2.jpg is the path to your background image */
            color: #fff;
            margin: 0;
            padding: 0;
        }

        .admin-view-container, .manage-membership-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #333;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .admin-view-container h1, .manage-membership-container h1 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
            color: #fff;
        }

        .admin-view-container table, .manage-membership-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .admin-view-container th, .admin-view-container td, .manage-membership-container th, .manage-membership-container td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .admin-view-container th {
            background-color: #f0f0f0;
            color: #333; /* Text color for table headers */
        }

        .admin-view-container a, .manage-membership-container a {
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
            color: #fff;
        }

        .admin-view-container a:hover, .manage-membership-container a:hover {
            background-color: #ddd;
        }

        .admin-view-container button, .manage-membership-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }

        .admin-view-container button:hover, .manage-membership-container button:hover {
            background-color: #0056b3;
        }

        .admin-view-container form, .manage-membership-container form {
            margin-bottom: 20px;
        }

        .admin-view-container label, .manage-membership-container label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }

        .admin-view-container input[type=text], .manage-membership-container input[type=text], .admin-view-container textarea, .manage-membership-container textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .admin-view-container textarea, .manage-membership-container textarea {
            resize: vertical;
            min-height: 100px;
        }

        .admin-view-container button[type=submit], .manage-membership-container button[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        .admin-view-container button[type=submit]:hover, .manage-membership-container button[type=submit]:hover {
            background-color: #45a049;
        }

        .admin-view-container p, .manage-membership-container p {
            margin-top: 20px;
            text-align: center;
        }

        .admin-view-container a.logout-link, .manage-membership-container a.logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            text-decoration: none;
            color: #007bff;
        }

        .admin-view-container a.logout-link:hover, .manage-membership-container a.logout-link:hover {
            text-decoration: underline;
        }

        .admin-view-container .error-message, .manage-membership-container .error-message {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .admin-view-container .success-message, .manage-membership-container .success-message {
            color: #28a745;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Styles specific to manage_membership.php */
        .manage-membership-container {
            position: relative;
        }

        .manage-membership-container h2 {
            color: #fff;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .manage-membership-container table {
            background-color: #666;
            color: #fff;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .manage-membership-container table th {
            background-color: #444;
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        .manage-membership-container table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        .manage-membership-container table td a {
            color: #fff;
            text-decoration: none;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .manage-membership-container table td a:hover {
            background-color: #dc3545;
        }

        .manage-membership-container form {
            margin-bottom: 20px;
        }

        .manage-membership-container label {
            display: block;
            margin-bottom: 5px;
            color: #fff;
        }

        .manage-membership-container input[type=text], .manage-membership-container textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .manage-membership-container textarea {
            resize: vertical;
            min-height: 100px;
        }

        .manage-membership-container button[type=submit] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            float: right;
        }

        .manage-membership-container button[type=submit]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="manage-membership-container">
    <button onclick="location.href='admin_view.php'" style="position: absolute; top: 10px; left: 10px; background-color: #28a745; color: #fff; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px;">Back to Admin Page</button>
        <h1>Manage Membership</h1>
        <!-- Memberships -->
        <h2>Memberships</h2>
        <table>
            <tr>
                <th>Email</th>
                <th>Plan</th>
                <th>Expiry Date</th>
                <th>Action</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($membership_result)) {
                echo "<tr>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['plan']."</td>";
                echo "<td>".$row['expiry_date']."</td>";
                /*<a href='update_membership.php?id=".$row['id']."'>Update</a> |*/
                echo "<td><a href='?delete_membership_id=".$row['id']."' style='background-color: #dc3545;'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- Membership Plans -->
        <h2>Membership Plans</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Features</th>
                <th>Action</th>
            </tr>
            <?php
            while ($row = mysqli_fetch_assoc($plan_result)) {
                echo "<tr>";
                echo "<td>".$row['name']."</td>";
                echo "<td>".$row['price']."</td>";
                echo "<td>".$row['features']."</td>";
                echo "<td><a href='?delete_plan_id=".$row['id']."' style='background-color: #dc3545;'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </table>

        <!-- Add New Plan -->
        <h2>Add New Membership Plan</h2>
        <form method="post" action="">
            <label for="plan_name">Plan Name:</label>
            <input type="text" id="plan_name" name="plan_name" required>
            <label for="plan_price">Plan Price:</label>
            <input type="text" id="plan_price" name="plan_price" required>
            <label for="plan_features">Plan Features:</label>
            <textarea id="plan_features" name="plan_features" required></textarea>
            <button type="submit" name="add_plan">Add Plan</button>
        </form>
    </div>
    
</body>
</html>

<?php
mysqli_close($con);
?>
