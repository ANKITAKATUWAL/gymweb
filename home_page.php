<?php
include 'db_connection.php';
session_start();
$user_id = $_SESSION['user_id']; // Start the session

if(isset($_POST['logout'])) {
  // Unset all of the session variables
  $_SESSION = array();

  // Destroy the session
  session_destroy();

  // Redirect to the login page
  header("Location: login.php");
  exit();}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fitness and Gym Club</title>
  <link rel="stylesheet" href="css/stylesheet.css">
  <link rel="stylesheet" href="css/product.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <style>
.user-icon {
  position: relative;
  display: inline-block;
  margin-right: 10px;
  cursor: pointer;
  color: #fff;
}
.user-icon .fas {
  font-size: 20px;
}
.sidebar-popup {
  display: none;
  position: absolute;
  top: 50px;
  right: 20px;
  background-color: #07373d;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
  z-index: 999;
}
.sidebar-popup.show {
  display: block;
}
.sidebar-popup span {
  font-size: 16px;
  font-weight: bold;
  color: #fff;
}
.sidebar-popup a {
  display: block;
  margin-top: 10px;
  text-decoration: none;
  color: #fff;
  transition: color 0.3s ease;
}
.sidebar-popup a:hover {
  color: #bbb;
}
.sidebar-popup button {
  margin-top: 10px;
  padding: 8px 16px;
  background-color: #f44336;
  color: #fff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.sidebar-popup button:hover {
  background-color: #d32f2f;
}
  </style>
 
</head>
<body>

  <header>
    <nav class="navbar">
      <div class="logo">
        <img src="css/image/logos.png" alt="Gym Logo">
        <span>Gym & Fitness Club</span>
      </div>
      <ul class="nav-links">
        <li><a href="membership_plan.php">Membership Plans</a></li> 
        <li><a href="#services">Services</a></li> 
        <li><a href="#classes">Classes</a></li>
        <li><a href="#contact">Contact Us</a></li>
        <li>
          <a href="#" class="user-icon"><i class="fas fa-user"></i></a>
          <div class="sidebar-popup">
            <span>Welcome,<br> <?php echo $_SESSION['email']; ?></span><br>
            <form method="post" action="">
                <button type="submit" name="logout">Logout</button>
            </form>
          </div>
        </li>

      </ul>
    </nav>
  </header>
  <section class="home-section" id="home">
    <div class="hero">
      <img src="css/image/logo.png" alt="Gym Hero Image">
      <h1>Welcome to Gym & Fitness Club</h1>
      <p>Your journey to a healthier you starts here.</p>
      <a href="membership_plan.php" class="cta-button">Get a Membership</a>
    </div>
    <?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>


    <div class="benefits" style="text-align: center; margin-top: 50px;">
  <h2 style="color: #07373d;">Membership Benefits</h2>
  <ul style="list-style-type: none; padding: 0;">
    <li style="margin-bottom: 10px;"><i class="fas fa-check-circle" style="color: #f44336;"></i> State-of-the-art facilities</li>
    <li style="margin-bottom: 10px;"><i class="fas fa-check-circle" style="color: #f44336;"></i> Expert personal trainers</li>
    <li style="margin-bottom: 10px;"><i class="fas fa-check-circle" style="color: #f44336;"></i> Wide range of classes</li>
    <li style="margin-bottom: 10px;"><i class="fas fa-check-circle" style="color: #f44336;"></i> Customized workout plans</li>
    <li style="margin-bottom: 10px;"><i class="fas fa-check-circle" style="color: #f44336;"></i> Supportive community</li>
  </ul>
</div>

    
    <h2>Featured Services</h2>
    <div class="services" id="services">
 
      <div class="service">
        <img src="css/image/trainer 1.jpg" alt="Service 1">
        <h3>Personal Training</h3>
        <p>One-on-one guidance for maximum results.</p>
      </div>
      <div class="service">
        <img src="css/image/trainer 3.jpg" alt="Service 2">
        <h3>Group Classes</h3>
        <p>Join our energetic group classes and stay motivated.</p>
      </div>
      <div class="service">
        <img src="css/image/practice session.jpg" alt="Service 2">
        <h3>Practice Sessions</h3>
        <p>Elevate your fitness with practice sessions..</p>
      </div>
      <div class="service">
        <img src="css/image/good management.jpg" alt="Service 2">
        <h3>Good Management</h3>
        <p>Supportive management, for your fitness success..</p>
      </div>
      
    </div> 
    
    <section class="classes-section" id="classes">
      <h2>Explore Our Diverse Classes</h2>
      <div class="class">
        <img src="css/image/yoga.jpg" alt="Yoga Class">
        <h3>Yoga</h3>
        <p>Unwind and strengthen your body and mind with our rejuvenating yoga classes. Experience tranquility as you flow through poses and focus on your breath. Our experienced instructors guide you through different styles, from Vinyasa to Hatha, helping you find your zen.</p>
        <div class="class-content">
          <h3>Class Timings</h3>
          <p>Monday & Wednesday<br>9:00 AM - 10:30 AM</p>
        </div>
      </div>
      <div class="class">
        <img src="css/image/hiit.jpg" alt="HIIT Class">
        <h3>High-Intensity Interval Training (HIIT)</h3>
        <p>Elevate your heart rate and ignite fat-burning with our dynamic HIIT classes. Push your limits with intense bursts of exercise followed by short recovery periods. Our certified trainers ensure you get a full-body workout, boosting your metabolism and increasing endurance.</p>
        <div class="class-content">
          <h3>Class Timings</h3>
          <p>Tuesday & Thursday<br>6:00 PM - 7:00 PM</p>
        </div>
      </div>
      <div class="class">
        <img src="css/image/spin.jpg" alt="Spinning Class">
        <h3>Spinning</h3>
        <p>Ride to new fitness heights with our exhilarating spinning classes. Pedal through challenging terrain, all while guided by energizing music and motivating instructors. Burn calories, improve cardiovascular health, and enjoy the camaraderie of fellow cyclists in a high-energy environment.</p>
        <div class="class-content">
          <h3>Class Timings</h3>
          <p>Wednesday & Friday<br>7:30 AM - 8:30 AM</p>
        </div>
      </div>
      <div class="class">
        <img src="css/image/pilates.jpg" alt="Pilates Class">
        <h3>Pilates</h3>
        <p>Transform your body and build core strength with our Pilates classes. Enhance flexibility, posture, and balance as you engage in low-impact exercises that target specific muscle groups. Our skilled instructors guide you through controlled movements that promote overall well-being.</p>
        <div class="class-content">
          <h3>Class Timings</h3>
          <p>Monday & Friday<br>5:00 PM - 6:00 PM</p>
        </div>
      </div>
    </section>
    
  <!-- Add more sections as needed (events, promotions, etc.) -->
  <section class="contact-section" id="contact">
    <h2>Contact Us</h2>
    <div class="contact-info">
      <div class="location">
        <h3>Location</h3>
        <p>Haleshi Chowk, Inaruwa</p>
        <p>Opposite to Nabil Bank</p>
      </div>
      <div class="contact-details">
        <h3>Contact Details</h3>
        <p>Phone: +97779810000000</p>
      </div>
    </div>
    <div class="social-icons">
      <a href="#"><img src="https://upload.wikimedia.org/wikipedia/en/thumb/0/04/Facebook_f_logo_%282021%29.svg/150px-Facebook_f_logo_%282021%29.svg.png" alt="Facebook"></a>
      <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Instagram_logo_2022.svg/150px-Instagram_logo_2022.svg.png" alt="Instagram"></a>
      <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Gmail_icon_%282020%29.svg/100px-Gmail_icon_%282020%29.svg.png" alt="Gmail"></a>
      <a href="#"><img src="https://www.viber.com/app/uploads/viber-logo.png" alt="Viber"></a>
      <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/120px-WhatsApp.svg.png" alt="WhatsApp"></a>
    </div>
    
  </section>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const userIcon = document.querySelector('.user-icon');
          const sidebarPopup = document.querySelector('.sidebar-popup');

          userIcon.addEventListener('click', function(event) {
             event.stopPropagation(); // Preventing propagation to prevent body click
             sidebarPopup.classList.toggle('show');
            });

           // Close sidebar on outside click
            document.addEventListener('click', function(event) {
                  if (!sidebarPopup.contains(event.target) && !userIcon.contains(event.target)) {
                     sidebarPopup.classList.remove('show');
                  }
            });
      });

  </script>
</body>
</html>