<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:autentificare.php');
};

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   $delete_message = $conn->prepare("DELETE FROM `review` WHERE id = ?");
   $delete_message->execute([$delete_id]);
   header('location:admin_review-uri.php');

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>review-uri</title>


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/adminmeniu.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>


<section class="review">

   <h1 class="title">review-uri</h1>

   <div class="box-container">

   <?php
      $select_message = $conn->prepare("SELECT * FROM `review`");
      $select_message->execute();
      if($select_message->rowCount() > 0){
         while($fetch_message = $select_message->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> userid : <span><?= $fetch_message['userid']; ?></span> </p>
      <p class="name"> name : <span><?= $fetch_message['name']; ?></span> </p>
      <p> review : <span><?= $fetch_message['reviews']; ?></span> </p>
      
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Nu ai review-uri noi!</p>';
      }
   ?>

   </div>

</section>

<script src="js/script.js"></script>

</body>
</html>