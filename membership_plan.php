<?php
include 'db_connection.php';
session_start(); // Start the session

// Retrieve user's details if logged in
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Retrieve user's subscription information
    $subscription_query = "SELECT * FROM membership WHERE email = '$email' AND expiry_date > NOW()";
    $subscription_result = mysqli_query($con, $subscription_query);

    if (mysqli_num_rows($subscription_result) > 0) {
        $subscription_row = mysqli_fetch_assoc($subscription_result);
        $subscription_plan = $subscription_row['plan'];
        $expiry_date = $subscription_row['expiry_date'];
    } else {
        $subscription_plan = "Not Subscribed";
        $expiry_date = "N/A";
    }
}

// Check if the form is submitted
if (isset($_POST['subscribe'])){
    // Retrieve the selected plan from the form
    $plan = $_POST['plan'];

    // Check if the user already has an active subscription
    if (isset($email)) {
        $check_query = "SELECT * FROM membership WHERE email = '$email' AND expiry_date > NOW()";
        $check_result = mysqli_query($con, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            // User already has an active subscription, show error message
            echo "<script>alert('You already have an active subscription!');</script>";
        } else {
            // User does not have an active subscription, proceed with subscription
            // Insert the subscription data into the membership table
            $insert_query = "INSERT INTO membership (email, plan, expiry_date) VALUES ('$email', '$plan', DATE_ADD(NOW(), INTERVAL 3 MONTH))";
            $insert_result = mysqli_query($con, $insert_query);

            if ($insert_result) {
                // Subscription successful, show success message
                echo "<script>alert('Successfully subscribed to $plan plan!');</script>";
            } else {
                // Subscription failed, handle the error accordingly
                echo "Subscription failed. Please try again.";
            }
        }
    }
}

// Handle cancel subscription
if (isset($_POST['cancel_subscription'])) {
    if (isset($email)) {
        $cancel_query = "DELETE FROM membership WHERE email = '$email' AND expiry_date > NOW()";
        $cancel_result = mysqli_query($con, $cancel_query);
        if ($cancel_result) {
            // Subscription cancellation successful, refresh the page
            header("Refresh:0");
            exit();
        }
    }
}

// Fetch all membership plans from the database
$plans_query = "SELECT * FROM membership_plans";
$plans_result = mysqli_query($con, $plans_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet"/>
  <link rel="stylesheet" href="css/first.css">
  <title>Membership Plan</title>
  <style>
    .user-info {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #fff;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .user-info p {
      margin: 5px 0;
    }

    .user-info button {
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <?php if (isset($_SESSION['email'])): ?>
  <section class="user-info">
    <div class="user-info__container">
      <p>Email: <?php echo $email; ?></p>
      <p>Subscription Plan: <?php echo $subscription_plan; ?></p>
      <p>Expiry Date: <?php echo $expiry_date; ?></p>
      <?php if ($subscription_plan !== "Not Subscribed"): ?>
      <form method="post" action="">
        <button class="btn" type="submit" name="cancel_subscription">Cancel My Subscription</button>
      </form>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>
  <section class="section__container price__container">
    <h2 class="section__header">OUR PRICING PLAN</h2>
    <p class="section__subheader">
      Our pricing plan comes with various membership tiers, each tailored to
      cater to different preferences and fitness aspirations.
    </p>
    <div class="price__grid">
      <?php while ($plan_row = mysqli_fetch_assoc($plans_result)): ?>
      <form method="post" action="">
        <div class="price__card">
          <div class="price__card__content">
            <h4><?php echo $plan_row['name']; ?></h4>
            <h3>Rs.<?php echo $plan_row['price']; ?></h3>
            <p><?php echo nl2br($plan_row['features']); ?></p>
          </div>
          <button class="btn price__btn" type="submit" name="subscribe" value="<?php echo $plan_row['name']; ?>">Subscribe Now</button>
          <input type="hidden" name="plan" value="<?php echo $plan_row['name']; ?>">
        </div>
      </form>
      <?php endwhile; ?>
    </div>
  </section>
</body>
</html>

<?php
mysqli_close($con);
?>
