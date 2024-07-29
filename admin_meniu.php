<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:autentificare.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin meniu</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="dashboard">

   <h1 class="title">panou admin</h1>


   <div class="box-container">

      <div class="box">
      <p>Raport vanzari</p>
      <h3><br></h3>
      <a href="adminraport.php" class="btn">vezi raport</a>
      </div>

      <div class="box">
      <p>Grafic vanzari</p>
      <h3><br></h3>
      <a href="admingraph.php" class="btn">vezi grafice</a>
      </div>

      <div class="box">
      <?php
         $select_orders = $conn->prepare("SELECT * FROM `orders`");
         $select_orders->execute();
         $number_of_orders = $select_orders->rowCount();
      ?>
      <p>Comenzi plasate</p>
      <h3><?= $number_of_orders; ?></h3>
      <a href="admin_comenzi.php" class="btn">vezi comenzi</a>
      </div>

      <div class="box">
      <?php
         $select_products = $conn->prepare("SELECT * FROM `products`");
         $select_products->execute();
         $number_of_products = $select_products->rowCount();
      ?>
      <p>Produse adăugate</p>
      <h3><?= $number_of_products; ?></h3>
      <a href="adminraport_produse.php" class="btn">vezi raport produse</a>
      </div>

      <div class="box">
      <?php
         $select_users = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
         $select_users->execute(['user']);
         $number_of_users = $select_users->rowCount();
      ?>
      <p> Nr utilizatori</p>
      <h3><?= $number_of_users; ?></h3>
      <a href="admin_utilizatori.php" class="btn">vezi conturi</a>
      </div>

      <div class="box">
      <?php
         $select_admins = $conn->prepare("SELECT * FROM `users` WHERE user_type = ?");
         $select_admins->execute(['admin']);
         $number_of_admins = $select_admins->rowCount();
      ?>
      <p>Admini</p>
      <h3><?= $number_of_admins; ?></h3>
      <a href="admin_modificare_profil.php" class="btn">Modificare cont</a>
      </div>

      <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `review`");
         $select_messages->execute();
         $number_of_messages = $select_messages->rowCount();
      ?>
      <p>Total review-uri</p>
      <h3><?= $number_of_messages; ?></h3>
      <a href="admin_review-uri.php" class="btn">vezi review-uri</a>
      </div>

      <div class="box">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM `message`");
         $select_messages->execute();
         $number_of_messages = $select_messages->rowCount();
      ?>
      <p>Total mesaje</p>
      <h3><?= $number_of_messages; ?></h3>
      <a href="admin_mesaje.php" class="btn">vezi mesaje</a>
      </div>

   </div>

</section>


<script src="js/script.js"></script>

</body>
</html>