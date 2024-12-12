<?php
include 'db_connection.php';

if(isset($_GET['uid'])) {
    $uid = $_GET['uid'];
    
    // Delete user data from the database
    $sql = "DELETE FROM register WHERE id='$uid'";
    $result = mysqli_query($con, $sql);
    
    if($result) {
        echo "User data deleted successfully!";
    } else {
        echo "Error deleting user data: " . mysqli_error($con);
    }
} else {
    echo "User ID not provided!";
}
mysqli_close($con);
?>
