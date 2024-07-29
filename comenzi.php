<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:autentificare.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Comenzi</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/general.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Comenzi plasate</h1>

   <div class="box-container">

   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
      $select_orders->execute([$user_id]);
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <p class="name"> Nume : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> Plasată la : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> Tel : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> Email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> Adresă : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> Metodă plată : <span><?= $fetch_orders['method']; ?></span> </p>
      <p> Detalii comandă : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> Pret total : <span><?= $fetch_orders['total_price']; ?>Lei</span> </p>
      <p> Status plată : <span style="color:<?php if($fetch_orders['payment_status'] == 'in asteptare'){ echo 'red'; }else{ echo 'green'; }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">Nu aveti comenzi plasate încă!</p>';
   }
   ?>

   </div>

</section>
<!------------------------------review ----------------->

<section class="reviewsform">
         <h1 class="title">review</h1>

   <form action="" method="POST">
      <input type="text" name="name" class="box" required placeholder="nume">
      <textarea name="msg" class="box" required placeholder="introduceți mesajul dumneavoastră" cols="30" rows="10"></textarea>
      <input type="submit" value="trimite review" class="btn" name="send">
   </form>
   <?php if (isset($_POST['send'])) {
      $username=$_POST['name'];
      $mes=$_POST['msg'];
      $userid=$_SESSION['user_id'];
      $insertsql="INSERT INTO `review`( `name`, `reviews`,`userid`) VALUES ('$username','$mes','$userid')";
      $conn->query($insertsql);
      echo '<p class="empty">Review trimis cu succes!</p>';
      }
   ?>
</section>





<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>

