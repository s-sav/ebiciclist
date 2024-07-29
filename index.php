<?php
@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];


if (isset($_POST['add_to_wishlist']) ) {
   if (!isset($_SESSION['user_id'])) {
      header('location: autentificare.php');
      exit();
}
   $pid = $_POST['pid'];
   $pid = filter_var($pid, );
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name );
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price );
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image );

   $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
   $check_wishlist_numbers->execute([$p_name, $user_id]);

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_wishlist_numbers->rowCount() > 0){
      $message[] = 'produsul este deja adăugat în favorite!';
   }elseif($check_cart_numbers->rowCount() > 0){
      $message[] = 'produsul este deja în coș!';
   }else{
      $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(user_id, pid, name, price, image) VALUES(?,?,?,?,?)");
      $insert_wishlist->execute([$user_id, $pid, $p_name, $p_price, $p_image]);
      $message[] = 'produs adăugat în favorite!';
   }

}

if(isset($_POST['add_to_cart'])){
   if (!isset($_SESSION['user_id'])) {
      header('location: autentificare.php');
      exit();
}

   $pid = $_POST['pid'];
   $pid = filter_var($pid );
   $p_name = $_POST['p_name'];
   $p_name = filter_var($p_name );
   $p_price = $_POST['p_price'];
   $p_price = filter_var($p_price );
   $p_image = $_POST['p_image'];
   $p_image = filter_var($p_image );
   $p_qty = $_POST['p_qty'];
   $p_qty = filter_var($p_qty );

   $check_cart_numbers = $conn->prepare("SELECT * FROM `cart` WHERE name = ? AND user_id = ?");
   $check_cart_numbers->execute([$p_name, $user_id]);

   if($check_cart_numbers->rowCount() > 0){
      $message[] = 'produsul este deja în coș!';
   }else{

      $check_wishlist_numbers = $conn->prepare("SELECT * FROM `wishlist` WHERE name = ? AND user_id = ?");
      $check_wishlist_numbers->execute([$p_name, $user_id]);

      if($check_wishlist_numbers->rowCount() > 0){
         $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE name = ? AND user_id = ?");
         $delete_wishlist->execute([$p_name, $user_id]);
      }

      $insert_cart = $conn->prepare("INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
      $insert_cart->execute([$user_id, $pid, $p_name, $p_price, $p_qty, $p_image]);
      $message[] = 'produs adăugat în coș!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>eBiciclist</title>
   <link rel="icon" type="image/x-icon" href="">


   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
   
   <link rel="stylesheet" href="css/general.css">
<style>
   a{
   text-decoration: none;
   }
</style>

</head>
<body>
   
<?php include 'header.php'; ?>

<!-----------------categorii--------->

<section class="home-category">

   <h1 class="title">categorii</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/categorie1.png" alt="">
         <h3>Biciclete</h3>
         <p>Mountain bike, cursiere, biciclete de oras. </p>
         <a href="categorii.php?category=biciclete" class="btn">Vezi produse</a>
      </div>

      <div class="box">
         <img src="images/categorie2.png" alt="">
         <h3>piese</h3>
         <p>Vrei să îți îmbunatățești performanța bicicletei tale? </p>
         <a href="categorii.php?category=piese" class="btn">Vezi produse</a>
      </div>

      <div class="box">
         <img src="images/categorie3.png" alt="">
         <h3>accesorii</h3>
         <p>Lumini, apărători, scule sau doar bidon și suport? </p>
         <a href="categorii.php?category=accesorii" class="btn">Vezi produse</a>
      </div>

      <div class="box">
         <img src="images/categorie4.png" alt="">
         <h3>echipamente</h3>
         <p>Cauți căști, veste, mănuși sau îmbrăcăminte? </p>
         <a href="categorii.php?category=echipamente" class="btn">Vezi produse</a>
      </div>

   </div>

</section>

<!-----------------recomandari produse--------->
<section class="products">

   <h1 class="title">Recomandări produse</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY `products`.`id` DESC LIMIT 6");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <form action="" class="box" method="POST">
      <div class="price"><span><?= $fetch_products['price']; ?></span> Lei</div>
      <a href="vizualizare_produs.php?pid=<?= $fetch_products['id']; ?>" class="fas fa-eye"></a>
      <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
      <input type="hidden" name="p_name" value="<?= $fetch_products['name']; ?>">
      <input type="hidden" name="p_price" value="<?= $fetch_products['price']; ?>">
      <input type="hidden" name="p_image" value="<?= $fetch_products['image']; ?>">
      <div class="latest-btn">
      <button type="submit" class="option-buttn" name="add_to_cart"><i class="fas fa-cart-shopping fa-bounce" style=" --fa-bounce-start-scale-x: 1; --fa-bounce-start-scale-y: 1; --fa-bounce-jump-scale-x: 1; --fa-bounce-jump-scale-y: 1; --fa-bounce-land-scale-x: 1; --fa-bounce-land-scale-y: 1; " ></i></button>
      <button type="submit" class="option-buttn" name="add_to_wishlist" ><i class="fa-solid fa-heart fa-beat"></i></button>
      <input type="number" min="0" value="1" name="p_qty" class="qty" step="1">
      </div>
   </form>
   <?php
      }
   }else{
      echo '<p class="empty">nu aveți produse adaugate încă!</p>';
   }
   ?>

   </div>
</section>

<!-- sectiune review -->

<section class="testimonial">
   <h1 class="title">Testimoniale</h1>
   <div class="box-container">
      <?php
        $select_message = $conn->prepare("SELECT * FROM `review`");
      $select_message->execute();
      if($select_message->rowCount() > 0){
            while($fetch_message = $select_message->fetch(PDO::FETCH_ASSOC)){
      ?>
               <blockquote>
                  <p><?= $fetch_message['reviews']; ?></p>
                  <footer><cite><?= $fetch_message['name']; ?></cite></footer>
               </blockquote>
      <?php
            }
      } else {
            echo '<p class="empty">Nu ai review-uri noi!</p>';
      }
      ?>
   </div>
</section>
<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>